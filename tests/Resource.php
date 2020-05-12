<?php

namespace Lampager\Laravel\Tests;

use Illuminate\Http\Resources\Json\Resource as BaseResource;
use Illuminate\Http\Resources\Json\JsonResource;

if (class_exists(BaseResource::class)) {
    /**
     * To support testing until Laravel version 7.
     */
    class Resource extends BaseResource
    {
    }
} else {
    /**
     * To support testing Laravel version 7 and up.
     */
    class Resource extends JsonResource
    {
    }
}
