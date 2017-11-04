<?php

namespace Lampager\Laravel;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\ServiceProvider;

/**
 * Class MacroServiceProvider
 */
class MacroServiceProvider extends ServiceProvider
{
    /**
     * Register "lampager" macros.
     */
    public function register()
    {
        QueryBuilder::macro('lampager', function () {
            /* @var \Illuminate\Database\Query\Builder $this */
            return Paginator::create($this);
        });
        EloquentBuilder::macro('lampager', function () {
            /* @var \Illuminate\Database\Eloquent\Builder $this */
            return Paginator::create($this);
        });
        Relation::macro('lampager', function () {
            /* @var \Illuminate\Database\Eloquent\Relations\Relation $this */
            return Paginator::create($this);
        });
    }
}
