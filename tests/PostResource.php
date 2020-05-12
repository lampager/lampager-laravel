<?php

namespace Lampager\Laravel\Tests;

use Lampager\Laravel\LampagerResourceTrait;

/**
 * Class PostResource
 */
class PostResource extends JsonResource
{
    use LampagerResourceTrait;

    public $preserveKeys = true;

    /**
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return parent::toArray($request) + [
            'post_resource' => true,
        ];
    }
}
