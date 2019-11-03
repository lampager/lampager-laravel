<?php

namespace Lampager\Laravel\Http\Resources;

use Illuminate\Http\Resources\CollectsResources;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Pagination\AbstractPaginator;
use Lampager\Laravel\PaginationResult;

/**
 * Trait CollectsPaginationResult
 *
 * @mixin \Illuminate\Http\Resources\Json\ResourceCollection
 */
trait CollectsPaginationResult
{
    use CollectsResources;

    /**
     * Map the given collection resource into its individual resources.
     *
     * @param  mixed $resource
     * @return mixed
     */
    protected function collectResource($resource)
    {
        if ($resource instanceof MissingValue) {
            return $resource;
        }

        $collects = $this->collects();

        $this->collection = $collects && !$resource->first() instanceof $collects
            ? $resource->mapInto($collects)
            : $resource->toBase();

        if ($resource instanceof AbstractPaginator) {
            $resource->setCollection($this->collection);
            return $resource;
        }
        if ($resource instanceof PaginationResult) {
            $resource->records = $this->collection;
            return $resource;
        }

        return $this->collection;
    }
}
