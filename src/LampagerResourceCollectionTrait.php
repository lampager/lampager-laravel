<?php

namespace Lampager\Laravel;

use Lampager\Laravel\Http\Resources\CollectsPaginationResult;
use Lampager\Laravel\Http\Resources\Json\MakesAnonymousPaginationResultAwareResourceCollection;
use Lampager\Laravel\Http\Resources\Json\RespondsWithPaginationResult;

/**
 * Trait LampagerResourceCollectionTrait
 */
trait LampagerResourceCollectionTrait
{
    use MakesAnonymousPaginationResultAwareResourceCollection,
        CollectsPaginationResult,
        RespondsWithPaginationResult;
}
