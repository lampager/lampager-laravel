<?php

namespace Lampager\Laravel\Http\Resources\Json;

use Illuminate\Http\Resources\Json\PaginatedResourceResponse;
use Illuminate\Pagination\AbstractPaginator;
use Lampager\Laravel\PaginationResult;

/**
 * Trait RespondsWithPaginationResult
 *
 * @mixin \Illuminate\Http\Resources\Json\ResourceCollection
 */
trait RespondsWithPaginationResult
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request      $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function toResponse($request)
    {
        if ($this->resource instanceof AbstractPaginator) {
            return (new PaginatedResourceResponse($this))->toResponse($request);
        }
        if ($this->resource instanceof PaginationResult) {
            return (new PaginationResultResourceResponse($this))->toResponse($request);
        }

        return parent::toResponse($request);
    }
}
