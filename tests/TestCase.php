<?php

namespace Lampager\Laravel\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Lampager\Laravel\MacroServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'test');
        $app['config']->set('database.connections.test', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);
    }

    /**
     * @param  \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            MacroServiceProvider::class,
        ];
    }

    protected function setUp()
    {
        parent::setUp();

        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->datetime('updated_at');
        });
        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
        });
        Schema::create('post_tag', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_id');
            $table->integer('tag_id');
        });

        Post::create(['id' => 1, 'updated_at' => '2017-01-01 10:00:00']);
        Post::create(['id' => 3, 'updated_at' => '2017-01-01 10:00:00']);
        Post::create(['id' => 5, 'updated_at' => '2017-01-01 10:00:00']);
        Post::create(['id' => 2, 'updated_at' => '2017-01-01 11:00:00']);
        Post::create(['id' => 4, 'updated_at' => '2017-01-01 11:00:00']);

        Tag::create(['id' => 1])->posts()->sync([1, 3, 5, 2, 4]);
    }
}
