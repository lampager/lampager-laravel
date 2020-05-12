<?php

namespace Lampager\Laravel\Http\Resources\Json;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Lampager\Laravel\Http\Resources\CollectsPaginationResult;

/**
 * Class AnonymousPaginationResultAwareResourceCollection
 *
 * @mixin \Illuminate\Http\Resources\Json\JsonResource
 */
class AnonymousPaginationResultAwareResourceCollection extends AnonymousResourceCollection
{
    use MakesAnonymousPaginationResultAwareResourceCollection,
        CollectsPaginationResult,
        RespondsWithPaginationResult;
}
