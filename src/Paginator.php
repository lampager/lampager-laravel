<?php

namespace Lampager\Laravel;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Traits\Macroable;
use Lampager\Concerns\HasProcessor;
use Lampager\Contracts\Cursor;
use Lampager\Paginator as BasePaginator;
use Lampager\Query;
use Lampager\Query\Select;
use Lampager\Query\SelectOrUnionAll;
use Lampager\Query\UnionAll;

/**
 * Class Paginator
 *
 * @see BasePaginator, HasProcessor
 */
class Paginator extends BasePaginator
{
    use Macroable, HasProcessor;

    /**
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Query\Builder $builder
     * @return static
     */
    public static function create($builder)
    {
        return new static($builder);
    }

    /**
     * Paginator constructor.
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Query\Builder $builder
     */
    public function __construct($builder)
    {
        $this->builder = $builder;
        $this->processor = new Processor();
    }

    /**
     * Build Illuminate Builder instance from Query config.
     *
     * @param  Query                                                                                                                     $query
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Query\Builder
     */
    public function transform(Query $query)
    {
        return $this->compileSelectOrUnionAll($query->selectOrUnionAll());
    }

    /**
     * Configure -> Transform.
     *
     * @param  Cursor|int[]|string[]                                                                                                     $cursor
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Query\Builder
     */
    public function build($cursor = [])
    {
        return $this->transform($this->configure($cursor));
    }

    /**
     * Execute query and paginate them.
     *
     * @param  Cursor|int[]|string[]  $cursor
     * @return mixed|PaginationResult
     */
    public function paginate($cursor = [])
    {
        $query = $this->configure($cursor);
        return $this->process($query, $this->transform($query)->get());
    }

    /**
     * @param  SelectOrUnionAll                                                                                                          $selectOrUnionAll
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Query\Builder
     */
    protected function compileSelectOrUnionAll(SelectOrUnionAll $selectOrUnionAll)
    {
        if ($selectOrUnionAll instanceof Select) {
            return $this->compileSelect($selectOrUnionAll);
        }
        if ($selectOrUnionAll instanceof UnionAll) {
            $supportQuery = $this->compileSelect($selectOrUnionAll->supportQuery());
            $mainQuery = $this->compileSelect($selectOrUnionAll->mainQuery());
            return $supportQuery->unionAll($this->addSelectForUnionAll($mainQuery));
        }
        // @codeCoverageIgnoreStart
        throw new \LogicException('Unreachable here');
        // @codeCoverageIgnoreEnd
    }

    /**
     * @param  Select                                                                                                                    $select
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Query\Builder
     */
    protected function compileSelect(Select $select)
    {
        $builder = clone $this->builder;
        $this
            ->compileWhere($builder, $select)
            ->compileOrderBy($builder, $select)
            ->compileLimit($builder, $select);
        return $builder;
    }

    /**
     * @param $builder \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Query\Builder
     * @param  Select $select
     * @return $this
     */
    protected function compileWhere($builder, Select $select)
    {
        $builder->where(function ($builder) use ($select) {
            foreach ($select->where() as $i => $group) {
                foreach ($group as $j => $condition) {
                    $builder->{$i !== 0 && $j === 0 ? 'orWhere' : 'where'}(
                        $this->transformPivotColumn($condition->left()),
                        $condition->comparator(),
                        $condition->right()
                    );
                }
            }
        });
        return $this;
    }

    /**
     * @param $builder \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Query\Builder
     * @param  Select $select
     * @return $this
     */
    protected function compileOrderBy($builder, Select $select)
    {
        foreach ($select->orders() as $order) {
            $builder->orderBy(...$order->toArray());
        }
        return $this;
    }

    /**
     * @param $builder \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Query\Builder
     * @param  Select $select
     * @return $this
     */
    protected function compileLimit($builder, Select $select)
    {
        $builder->limit($select->limit()->toInteger());
        return $this;
    }

    /**
     * We need to add columns explicitly for UNION ALL subjects
     * because BelongsToMany cannot handle them correctly.
     *
     * @param  \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Query\Builder
     */
    protected function addSelectForUnionAll($query)
    {
        static $invoker;
        if (!$invoker) {
            $invoker = function () {
                return $this->shouldSelect($this->getBaseQuery()->columns ? [] : ['*']);
            };
        }
        return $query instanceof BelongsToMany
            ? $query->addSelect($invoker->bindTo($query, $query)->__invoke())
            : $query;
    }

    /**
     * We need to transform aliased columns into non-aliased form
     * because SQL standard does not allow column aliases in WHERE conditions.
     *
     * @param  string $column
     * @return string
     */
    protected function transformPivotColumn($column)
    {
        return $this->builder instanceof BelongsToMany && strpos($column, 'pivot_') === 0
            ? ($this->builder->getTable() . '.' . substr($column, 6))
            : $column;
    }
}
