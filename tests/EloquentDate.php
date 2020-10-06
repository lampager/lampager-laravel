<?php

namespace Lampager\Laravel\Tests;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;

class EloquentDate
{
    public static function format(string $date): string
    {
        return version_compare(App::version(), '7', '>')
            ? Carbon::parse($date)->toJSON()
            : (string)Carbon::parse($date);
    }
}
