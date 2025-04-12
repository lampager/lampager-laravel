<?php

namespace Lampager\Laravel\Tests;

use PHPUnit\Framework\Attributes\Test;

class MacroTest extends TestCase
{
    #[Test]
    public function registerAllIlluminateMacros(): void
    {
        (new Post())->belongsTo(Post::class)->lampager()->orderBy('id')->build()->toSql();
        $x = (new Post())->lampager()->orderBy('id')->build()->toSql();
        $y = (new Post())->newQuery()->getQuery()->lampager()->orderBy('id')->build()->toSql();
        $this->assertEquals($x, $y);
    }
}
