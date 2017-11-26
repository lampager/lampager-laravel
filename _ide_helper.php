<?php

/**
 * A helper file for Laravel 5, to provide autocomplete information to your IDE
 *
 * @see https://github.com/barryvdh/laravel-ide-helper
 */

namespace
{
    exit('This file should not be included, only analyzed by your IDE');
}

namespace Illuminate\Database\Query
{
    class Builder
    {
        /**
         * Wrap Query Builder with a Paginator instance.
         *
         * @return \Lampager\Laravel\Paginator
         */
        public function lampager()
        {
            return new \Lampager\Laravel\Paginator(null);
        }
    }
}

namespace Illuminate\Database\Eloquent
{
    class Model
    {
        /**
         * Wrap Eloquent Builder with a Paginator instance.
         *
         * @return \Lampager\Laravel\Paginator
         */
        public function lampager()
        {
            return new \Lampager\Laravel\Paginator(null);
        }
    }
    class Builder
    {
        /**
         * Wrap Eloquent Builder with a Paginator instance.
         *
         * @return \Lampager\Laravel\Paginator
         */
        public function lampager()
        {
            return new \Lampager\Laravel\Paginator(null);
        }
    }
}

namespace Illuminate\Database\Eloquent\Relations
{
    class Relation
    {
        /**
         * Wrap Relation with a Paginator instance.
         *
         * @return \Lampager\Laravel\Paginator
         */
        public function lampager()
        {
            return new \Lampager\Laravel\Paginator(null);
        }
    }
}
