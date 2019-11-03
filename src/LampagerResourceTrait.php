<?php

namespace Lampager\Laravel;

use Lampager\Laravel\Http\Resources\Json\MakesAnonymousPaginationResultAwareResourceCollection;

/**
 * Trait LampagerResourceTrait
 */
trait LampagerResourceTrait
{
    use MakesAnonymousPaginationResultAwareResourceCollection;
}
