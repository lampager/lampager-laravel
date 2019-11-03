<?php

namespace Lampager\Laravel\Tests;

use Illuminate\Http\Resources\Json\JsonResource;
use Lampager\Laravel\LampagerResourceTrait;

/**
 * Class TagResource
 */
class TagResource extends JsonResource
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
