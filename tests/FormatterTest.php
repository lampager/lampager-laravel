<?php

namespace Lampager\Laravel\Tests;

use Illuminate\Support\Collection;
use Lampager\Laravel\Processor;
use Lampager\Query;
use PHPUnit\Framework\Attributes\Test;

class FormatterTest extends TestCase
{
    #[Test]
    public function testStaticCustomFormatter(): void
    {
        try {
            Processor::setDefaultFormatter(function ($rows, $meta, Query $query) {
                $this->assertInstanceOf(Post::class, $query->builder()->getModel());
                $meta['foo'] = 'bar';
                return new Collection([
                    'records' => $rows,
                    'meta' => $meta,
                ]);
            });
            $result = Post::lampager()->orderBy('id')->paginate();
            $this->assertInstanceOf(Collection::class, $result);
            $this->assertEquals('bar', $result['meta']['foo']);
        } finally {
            Processor::restoreDefaultFormatter();
        }
    }

    #[Test]
    public function testInstanceCustomFormatter(): void
    {
        $pager = Post::lampager();
        try {
            $result = $pager->orderBy('id')->useFormatter(function ($rows, $meta, Query $query) {
                $this->assertInstanceOf(Post::class, $query->builder()->getModel());
                $meta['foo'] = 'bar';
                return new Collection([
                    'records' => $rows,
                    'meta' => $meta,
                ]);
            })->paginate();
            $this->assertInstanceOf(Collection::class, $result);
            $this->assertEquals('bar', $result['meta']['foo']);
        } finally {
            $pager->restoreFormatter();
        }
    }

    #[Test]
    public function testInvalidFormatter(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Post::lampager()->useProcessor(function () {});
    }

    #[Test]
    public function testInvalidProcessor(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        Post::lampager()->useFormatter(__CLASS__);
    }
}
