<?php

namespace Lampager\Laravel;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\Macroable;
use Lampager\Concerns\HasProcessor;
use Lampager\Paginator as BasePaginator;
use Lampager\Query\Query;
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
     * IlluminateFactory constructor.
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
     * @param  int[]|string[]                                                                                                            $cursor
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation|\Illuminate\Database\Query\Builder
     */
    public function build(array $cursor = [])
    {
        return $this->transform($this->configure($cursor));
    }

    /**
     * Execute query and paginate them.
     *
     * @param  int[]|string[] $cursor
     * @return Collection
     */
    public function paginate(array $cursor = [])
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
            return $supportQuery->unionAll($mainQuery);
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
            foreach ($select->where() ?: [] as $i => $group) {
                foreach ($group as $j => $condition) {
                    $builder->{$i !== 0 && $j === 0 ? 'orWhere' : 'where'}(...$condition->toArray());
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
}
