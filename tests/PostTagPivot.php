<?php

namespace Lampager\Laravel\Tests;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Class PostTagPivot
 */
class PostTagPivot extends Pivot
{
    protected $casts = [
        'id' => 'int',
    ];
}
