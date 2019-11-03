<?php

namespace Lampager\Laravel\Tests;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Lampager\Laravel\LampagerResourceCollectionTrait;

/**
 * Class PostResourceCollection
 */
class PostResourceCollection extends ResourceCollection
{
    use LampagerResourceCollectionTrait;
}
