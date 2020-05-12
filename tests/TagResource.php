<?php

namespace Lampager\Laravel\Tests;

use Lampager\Laravel\LampagerResourceTrait;

/**
 * Class TagResource
 */
class TagResource extends Resource
{
    use LampagerResourceTrait;

    /**
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return array_replace(parent::toArray($request), [
            'posts' => new PostResourceCollection($this->whenLoaded('posts')),
        ]);
    }
}
