<?php

namespace Lampager\Laravel\Tests;

class MacroTest extends TestCase
{
    /**
     * @test
     */
    public function registerAllIlluminateMacros()
    {
        (new Post())->belongsTo(Post::class)->lampager()->orderBy('id')->build()->toSql();
        $x = (new Post())->lampager()->orderBy('id')->build()->toSql();
        $y = (new Post())->newQuery()->getQuery()->lampager()->orderBy('id')->build()->toSql();
        $this->assertEquals($x, $y);
    }
}
