<?php

namespace Lampager\Laravel\Tests;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Lampager\Laravel\LampagerResourceCollectionTrait;

/**
 * Class PostResourceCollection
 */
class StructuredPostResourceCollection extends ResourceCollection
{
    use LampagerResourceCollectionTrait;

    public $collects = PostResource::class;

    /**
     * Get additional data that should be returned with the resource array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request)
    {
        return [
            'post_resource_collection' => true,
        ];
    }
}
