<?php

namespace Lampager\Laravel\Tests;

use Carbon\Carbon;

class EloquentDate
{
    public static function format(string $date): string
    {
        return Carbon::parse($date)->toJSON();
    }
}
