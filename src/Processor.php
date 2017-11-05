<?php

namespace Lampager\Laravel;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
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
     * Return comparable value from a row.
     *
     * @param  mixed      $row
     * @param  string     $column
     * @return int|string
     */
    protected function field($row, $column)
    {
        $value = $row->$column;
        return is_object($value) ? (string)$value : $value;
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
