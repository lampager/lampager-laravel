<?php

namespace Lampager\Laravel\Tests;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;

class TestDate
{
    public static function format(string $date): string
    {
        return Carbon::parse($date)->format(
            version_compare(App::version(), '7', '>')
                ? Carbon::ISO8601
                : Carbon::DEFAULT_TO_STRING_FORMAT
        );
    }
}
