<?php

namespace Lampager\Laravel;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Lampager\PaginationResult as BasePaginationResult;

/**
 * PaginationResult
 *
 * @see BasePaginationResult
 * @mixin Collection
 */
class PaginationResult extends BasePaginationResult implements \JsonSerializable
{
    use Macroable {
        __call as macroCall;
    }

    /**
     * Make dynamic calls into the collection.
     *
     * @param  string $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, array $parameters)
    {
        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return $this->records->$method(...$parameters);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach (get_object_vars($this) as $key => $value) {
            $array[Str::snake($key)] = $value;
        }
        return $array;
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Convert the object to its JSON representation.
     *
     * @param  int    $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }
}
