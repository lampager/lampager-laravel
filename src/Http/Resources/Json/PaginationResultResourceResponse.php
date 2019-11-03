<?php

namespace Lampager\Laravel\Http\Resources\Json;

use Illuminate\Http\Resources\Json\PaginatedResourceResponse;
use Illuminate\Support\Arr;

/**
 * class PaginationResultResourceResponse
 */
class PaginationResultResourceResponse extends PaginatedResourceResponse
{
    /**
     * Add the pagination information to the response.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    protected function paginationInformation($request)
    {
        return Arr::except($this->resource->resource->toArray(), 'records');
    }
}
