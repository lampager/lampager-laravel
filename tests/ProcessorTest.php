<?php

namespace Lampager\Laravel\Tests;

class ProcessorTest extends TestCase
{
    /**
     * @param $expected
     * @param $actual
     */
    protected function assertResultEquals($expected, $actual)
    {
        $this->assertEquals(
            json_decode(json_encode($expected)),
            json_decode(json_encode($actual))
        );
    }

    /**
     * @test
     */
    public function testAscendingForwardStartInclusive()
    {
        $this->assertResultEquals(
            [
                'records' => [
                    ['id' => 1, 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => 3, 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => 5, 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'meta' => [
                    'next_cursor' => ['id' => 2, 'updated_at' => '2017-01-01 11:00:00'],
                ],
            ],
            Post::lampager()
            ->forward()->limit(3)
            ->orderBy('updated_at')
            ->orderBy('id')
            ->seekable()
            ->paginate()
        );
    }

    /**
     * @test
     */
    public function testAscendingForwardStartExclusive()
    {
        $this->assertResultEquals(
            [
                'records' => [
                    ['id' => 1, 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => 3, 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => 5, 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'meta' => [
                    'next_cursor' => ['id' => 5, 'updated_at' => '2017-01-01 10:00:00'],
                ],
            ],
            Post::lampager()
                ->forward()->limit(3)
                ->orderBy('updated_at')
                ->orderBy('id')
                ->seekable()
                ->exclusive()
                ->paginate()
        );
    }

    /**
     * @test
     */
    public function testAscendingForwardInclusive()
    {
        $this->assertResultEquals(
            [
                'records' => [
                    ['id' => 3, 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => 5, 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => 2, 'updated_at' => '2017-01-01 11:00:00'],
                ],
                'meta' => [
                    'previous_cursor' => ['id' => 1, 'updated_at' => '2017-01-01 10:00:00'],
                    'next_cursor' => ['id' => 4, 'updated_at' => '2017-01-01 11:00:00'],
                ],
            ],
            Post::lampager()
                ->forward()->limit(3)
                ->orderBy('updated_at')
                ->orderBy('id')
                ->seekable()
                ->paginate(['id' => 3, 'updated_at' => '2017-01-01 10:00:00'])
        );
    }

    /**
     * @test
     */
    public function testAscendingForwardExclusive()
    {
        $this->assertResultEquals(
            [
                'records' => [
                    ['id' => 5, 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => 2, 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => 4, 'updated_at' => '2017-01-01 11:00:00'],
                ],
                'meta' => [
                    'previous_cursor' => ['id' => 5, 'updated_at' => '2017-01-01 10:00:00'],
                    'next_cursor' => null,
                ],
            ],
            Post::lampager()
                ->forward()->limit(3)
                ->orderBy('updated_at')
                ->orderBy('id')
                ->seekable()
                ->exclusive()
                ->paginate(['id' => 3, 'updated_at' => '2017-01-01 10:00:00'])
        );
    }

    /**
     * @test
     */
    public function testAscendingBackwardStartInclusive()
    {
        $this->assertResultEquals(
            [
                'records' => [
                    ['id' => 5, 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => 2, 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => 4, 'updated_at' => '2017-01-01 11:00:00'],
                ],
                'meta' => [
                    'previous_cursor' => ['id' => 3, 'updated_at' => '2017-01-01 10:00:00'],
                ],
            ],
            Post::lampager()
                ->backward()->limit(3)
                ->orderBy('updated_at')
                ->orderBy('id')
                ->seekable()
                ->paginate()
        );
    }

    /**
     * @test
     */
    public function testAscendingBackwardStartExclusive()
    {
        $this->assertResultEquals(
            [
                'records' => [
                    ['id' => 5, 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => 2, 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => 4, 'updated_at' => '2017-01-01 11:00:00'],
                ],
                'meta' => [
                    'previous_cursor' => ['id' => 5, 'updated_at' => '2017-01-01 10:00:00'],
                ],
            ],
            Post::lampager()
                ->backward()->limit(3)
                ->orderBy('updated_at')
                ->orderBy('id')
                ->seekable()
                ->exclusive()
                ->paginate()
        );
    }

    /**
     * @test
     */
    public function testAscendingBackwardInclusive()
    {
        $this->assertResultEquals(
            [
                'records' => [
                    ['id' => 1, 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => 3, 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'meta' => [
                    'previous_cursor' => null,
                    'next_cursor' => ['id' => 5, 'updated_at' => '2017-01-01 10:00:00'],
                ],
            ],
            Post::lampager()
                ->backward()->limit(3)
                ->orderBy('updated_at')
                ->orderBy('id')
                ->seekable()
                ->paginate(['id' => 3, 'updated_at' => '2017-01-01 10:00:00'])
        );
    }

    /**
     * @test
     */
    public function testAscendingBackwardExclusive()
    {
        $this->assertResultEquals(
            [
                'records' => [
                    ['id' => 1, 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'meta' => [
                    'previous_cursor' => null,
                    'next_cursor' => ['id' => 1, 'updated_at' => '2017-01-01 10:00:00'],
                ],
            ],
            Post::lampager()
                ->backward()->limit(3)
                ->orderBy('updated_at')
                ->orderBy('id')
                ->seekable()
                ->exclusive()
                ->paginate(['id' => 3, 'updated_at' => '2017-01-01 10:00:00'])
        );
    }

    /**
     * @test
     */
    public function testDescendingForwardStartInclusive()
    {
        $this->assertResultEquals(
            [
                'records' => [
                    ['id' => 4, 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => 2, 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => 5, 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'meta' => [
                    'next_cursor' => ['id' => 3, 'updated_at' => '2017-01-01 10:00:00'],
                ],
            ],
            Post::lampager()
                ->forward()->limit(3)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->seekable()
                ->paginate()
        );
    }

    /**
     * @test
     */
    public function testDescendingForwardStartExclusive()
    {
        $this->assertResultEquals(
            [
                'records' => [
                    ['id' => 4, 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => 2, 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => 5, 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'meta' => [
                    'next_cursor' => ['id' => 5, 'updated_at' => '2017-01-01 10:00:00'],
                ],
            ],
            Post::lampager()
                ->forward()->limit(3)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->seekable()
                ->exclusive()
                ->paginate()
        );
    }

    /**
     * @test
     */
    public function testDescendingForwardInclusive()
    {
        $this->assertResultEquals(
            [
                'records' => [
                    ['id' => 3, 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => 1, 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'meta' => [
                    'previous_cursor' => ['id' => 5, 'updated_at' => '2017-01-01 10:00:00'],
                    'next_cursor' => null,
                ],
            ],
            Post::lampager()
                ->forward()->limit(3)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->seekable()
                ->paginate(['id' => 3, 'updated_at' => '2017-01-01 10:00:00'])
        );
    }

    /**
     * @test
     */
    public function testDescendingForwardExclusive()
    {
        $this->assertResultEquals(
            [
                'records' => [
                    ['id' => 1, 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'meta' => [
                    'previous_cursor' => ['id' => 1, 'updated_at' => '2017-01-01 10:00:00'],
                    'next_cursor' => null,
                ],
            ],
            Post::lampager()
                ->forward()->limit(3)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->seekable()
                ->exclusive()
                ->paginate(['id' => 3, 'updated_at' => '2017-01-01 10:00:00'])
        );
    }

    /**
     * @test
     */
    public function testDescendingBackwardStartInclusive()
    {
        $this->assertResultEquals(
            [
                'records' => [
                    ['id' => 5, 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => 3, 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => 1, 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'meta' => [
                    'previous_cursor' => ['id' => 2, 'updated_at' => '2017-01-01 11:00:00'],
                ],
            ],
            Post::lampager()
                ->backward()->limit(3)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->seekable()
                ->paginate()
        );
    }

    /**
     * @test
     */
    public function testDescendingBackwardStartExclusive()
    {
        $this->assertResultEquals(
            [
                'records' => [
                    ['id' => 5, 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => 3, 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => 1, 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'meta' => [
                    'previous_cursor' => ['id' => 5, 'updated_at' => '2017-01-01 10:00:00'],
                ],
            ],
            Post::lampager()
                ->backward()->limit(3)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->seekable()
                ->exclusive()
                ->paginate()
        );
    }

    /**
     * @test
     */
    public function testDescendingBackwardInclusive()
    {
        $this->assertResultEquals(
            [
                'records' => [
                    ['id' => 2, 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => 5, 'updated_at' => '2017-01-01 10:00:00'],
                    ['id' => 3, 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'meta' => [
                    'previous_cursor' => ['id' => 4, 'updated_at' => '2017-01-01 11:00:00'],
                    'next_cursor' => ['id' => 1, 'updated_at' => '2017-01-01 10:00:00'],
                ],
            ],
            Post::lampager()
                ->backward()->limit(3)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->seekable()
                ->paginate(['id' => 3, 'updated_at' => '2017-01-01 10:00:00'])
        );
    }

    /**
     * @test
     */
    public function testDescendingBackwardExclusive()
    {
        $this->assertResultEquals(
            [
                'records' => [
                    ['id' => 4, 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => 2, 'updated_at' => '2017-01-01 11:00:00'],
                    ['id' => 5, 'updated_at' => '2017-01-01 10:00:00'],
                ],
                'meta' => [
                    'previous_cursor' => null,
                    'next_cursor' => ['id' => 5, 'updated_at' => '2017-01-01 10:00:00'],
                ],
            ],
            Post::lampager()
                ->backward()->limit(3)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->seekable()
                ->exclusive()
                ->paginate(['id' => 3, 'updated_at' => '2017-01-01 10:00:00'])
        );
    }
}
