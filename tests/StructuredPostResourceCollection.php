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
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            static::$wrap => parent::toArray($request),
            'post_resource_collection' => true,
        ];
    }
}
