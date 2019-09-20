<?php

namespace Lampager\Laravel;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Traits\Macroable;
use Lampager\AbstractProcessor;
use Lampager\Query;

/**
 * Class Processor
 *
 * @see AbstractProcessor
 */
class Processor extends AbstractProcessor
{
    use Macroable;

    /**
     * @var mixed
     */
    protected $builder;

    /**
     * Get result.
     *
     * @param  Query                       $query
     * @param  Collection|Model[]|object[] $rows
     * @return mixed
     */
    public function process(Query $query, $rows)
    {
        $this->builder = $query->builder();
        return parent::process($query, $rows);
    }

    /**
     * Return comparable value from a row.
     *
     * @param  mixed      $row
     * @param  string     $column
     * @return int|string
     */
    protected function field($row, $column)
    {
        if ($this->builder instanceof BelongsToMany && strpos($column, 'pivot_') === 0) {
            return $this->pivotField($row, substr($column, 6), $this->pivotAccessor());
        }
        $value = $row->$column;
        return is_object($value) ? (string)$value : $value;
    }

    /**
     * Extract pivot from a row.
     *
     * @param  mixed      $row
     * @param  string     $column
     * @param  string     $accessor
     * @throws \Exception
     * @return int|string
     */
    protected function pivotField($row, $column, $accessor)
    {
        $pivot = $row->$accessor;
        if (!isset($pivot->$column)) {
            throw new \Exception("The column `$column` is not included in the pivot \"$accessor\".");
        }
        return $this->field($pivot, $column);
    }

    /**
     * Extract pivot accessor from a relation.
     *
     * @return string
     */
    protected function pivotAccessor()
    {
        return $this->builder->getPivotAccessor();
    }

    /**
     * Return the n-th element of collection.
     * Must return null if not exists.
     *
     * @param  Collection|Model[]|object[] $rows
     * @param  int                         $offset
     * @return Model|object
     */
    protected function offset($rows, $offset)
    {
        return isset($rows[$offset]) ? $rows[$offset] : null;
    }

    /**
     * Slice rows, like PHP function array_slice().
     *
     * @param  Collection|Model[]|object[] $rows
     * @param  int                         $offset
     * @param  null|int                    $length
     * @return Collection|Model[]|object[]
     */
    protected function slice($rows, $offset, $length = null)
    {
        return $rows->slice($offset, $length)->values();
    }

    /**
     * Count rows, like PHP function count().
     *
     * @param  Collection|Model[]|object[] $rows
     * @return int
     */
    protected function count($rows)
    {
        return $rows->count();
    }

    /**
     * Reverse rows, like PHP function array_reverse().
     *
     * @param  Collection|Model[]|object[] $rows
     * @return Collection|Model[]|object[]
     */
    protected function reverse($rows)
    {
        return $rows->reverse()->values();
    }

    /**
     * Format result.
     *
     * @param  Collection|Model[]|object[] $rows
     * @param  array                       $meta
     * @param  Query                       $query
     * @return PaginationResult
     */
    protected function defaultFormat($rows, array $meta, Query $query)
    {
        return new PaginationResult($rows, $meta);
    }
}
