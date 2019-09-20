<?php

namespace Lampager\Laravel\Tests;

use Illuminate\Support\Collection;
use Lampager\Laravel\Processor;
use Lampager\Query;

class FormatterTest extends TestCase
{
    /**
     * @test
     */
    public function testStaticCustomFormatter()
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

    /**
     * @test
     */
    public function testInstanceCustomFormatter()
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

    /**
     * @test
     */
    public function testInvalidFormatter()
    {
        $this->expectException(\InvalidArgumentException::class);
        Post::lampager()->useProcessor(function () {});
    }

    /**
     * @test
     */
    public function testInvalidProcessor()
    {
        $this->expectException(\InvalidArgumentException::class);
        Post::lampager()->useFormatter(__CLASS__);
    }
}
