<?php

namespace Lampager\Laravel\Tests;

use PHPUnit\Framework\Attributes\Test;

class ProcessorTest extends TestCase
{
    /**
     * @param $expected
     * @param $actual
     */
    protected function assertResultSame($expected, $actual)
    {
        $this->assertSame(
            json_decode(json_encode($expected), true),
            json_decode(json_encode($actual), true)
        );
    }

    #[Test]
    public function testAscendingForwardStartInclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => 1, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                    ['id' => 3, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                    ['id' => 5, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                ],
                'has_previous' => null,
                'previous_cursor' => null,
                'has_next' => true,
                'next_cursor' => ['updated_at' => '2017-01-01 11:00:00', 'id' => 2],
            ],
            Post::lampager()
                ->forward()->limit(3)
                ->orderBy('updated_at')
                ->orderBy('id')
                ->seekable()
                ->paginate()
        );
    }

    #[Test]
    public function testAscendingForwardStartExclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => 1, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                    ['id' => 3, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                    ['id' => 5, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                ],
                'has_previous' => null,
                'previous_cursor' => null,
                'has_next' => true,
                'next_cursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => 5],
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

    #[Test]
    public function testAscendingForwardInclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => 3, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                    ['id' => 5, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                    ['id' => 2, 'updated_at' => EloquentDate::format('2017-01-01 11:00:00')],
                ],
                'has_previous' => true,
                'previous_cursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => 1],
                'has_next' => true,
                'next_cursor' => ['updated_at' => '2017-01-01 11:00:00', 'id' => 4],
            ],
            Post::lampager()
                ->forward()->limit(3)
                ->orderBy('updated_at')
                ->orderBy('id')
                ->seekable()
                ->paginate(['id' => 3, 'updated_at' => '2017-01-01 10:00:00'])
        );
    }

    #[Test]
    public function testAscendingForwardExclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => 5, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                    ['id' => 2, 'updated_at' => EloquentDate::format('2017-01-01 11:00:00')],
                    ['id' => 4, 'updated_at' => EloquentDate::format('2017-01-01 11:00:00')],
                ],
                'has_previous' => true,
                'previous_cursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => 5],
                'has_next' => false,
                'next_cursor' => null,
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

    #[Test]
    public function testAscendingBackwardStartInclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => 5, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                    ['id' => 2, 'updated_at' => EloquentDate::format('2017-01-01 11:00:00')],
                    ['id' => 4, 'updated_at' => EloquentDate::format('2017-01-01 11:00:00')],
                ],
                'has_previous' => true,
                'previous_cursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => 3],
                'has_next' => null,
                'next_cursor' => null,
            ],
            Post::lampager()
                ->backward()->limit(3)
                ->orderBy('updated_at')
                ->orderBy('id')
                ->seekable()
                ->paginate()
        );
    }

    #[Test]
    public function testAscendingBackwardStartExclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => 5, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                    ['id' => 2, 'updated_at' => EloquentDate::format('2017-01-01 11:00:00')],
                    ['id' => 4, 'updated_at' => EloquentDate::format('2017-01-01 11:00:00')],
                ],
                'has_previous' => true,
                'previous_cursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => 5],
                'has_next' => null,
                'next_cursor' => null,
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

    #[Test]
    public function testAscendingBackwardInclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => 1, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                    ['id' => 3, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                ],
                'has_previous' => false,
                'previous_cursor' => null,
                'has_next' => true,
                'next_cursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => 5],
            ],
            Post::lampager()
                ->backward()->limit(3)
                ->orderBy('updated_at')
                ->orderBy('id')
                ->seekable()
                ->paginate(['id' => 3, 'updated_at' => '2017-01-01 10:00:00'])
        );
    }

    #[Test]
    public function testAscendingBackwardExclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => 1, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                ],
                'has_previous' => false,
                'previous_cursor' => null,
                'has_next' => true,
                'next_cursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => 1],
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

    #[Test]
    public function testDescendingForwardStartInclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => 4, 'updated_at' => EloquentDate::format('2017-01-01 11:00:00')],
                    ['id' => 2, 'updated_at' => EloquentDate::format('2017-01-01 11:00:00')],
                    ['id' => 5, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                ],
                'has_previous' => null,
                'previous_cursor' => null,
                'has_next' => true,
                'next_cursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => 3],
            ],
            Post::lampager()
                ->forward()->limit(3)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->seekable()
                ->paginate()
        );
    }

    #[Test]
    public function testDescendingForwardStartExclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => 4, 'updated_at' => EloquentDate::format('2017-01-01 11:00:00')],
                    ['id' => 2, 'updated_at' => EloquentDate::format('2017-01-01 11:00:00')],
                    ['id' => 5, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                ],
                'has_previous' => null,
                'previous_cursor' => null,
                'has_next' => true,
                'next_cursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => 5],
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

    #[Test]
    public function testDescendingForwardInclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => 3, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                    ['id' => 1, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                ],
                'has_previous' => true,
                'previous_cursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => 5],
                'has_next' => false,
                'next_cursor' => null,
            ],
            Post::lampager()
                ->forward()->limit(3)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->seekable()
                ->paginate(['id' => 3, 'updated_at' => '2017-01-01 10:00:00'])
        );
    }

    #[Test]
    public function testDescendingForwardExclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => 1, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                ],
                'has_previous' => true,
                'previous_cursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => 1],
                'has_next' => false,
                'next_cursor' => null,
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

    #[Test]
    public function testDescendingBackwardStartInclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => 5, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                    ['id' => 3, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                    ['id' => 1, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                ],
                'has_previous' => true,
                'previous_cursor' => ['updated_at' => '2017-01-01 11:00:00', 'id' => 2],
                'has_next' => null,
                'next_cursor' => null,
            ],
            Post::lampager()
                ->backward()->limit(3)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->seekable()
                ->paginate()
        );
    }

    #[Test]
    public function testDescendingBackwardStartExclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => 5, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                    ['id' => 3, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                    ['id' => 1, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                ],
                'has_previous' => true,
                'previous_cursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => 5],
                'has_next' => null,
                'next_cursor' => null,
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

    #[Test]
    public function testDescendingBackwardInclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => 2, 'updated_at' => EloquentDate::format('2017-01-01 11:00:00')],
                    ['id' => 5, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                    ['id' => 3, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                ],
                'has_previous' => true,
                'previous_cursor' => ['updated_at' => '2017-01-01 11:00:00', 'id' => 4],
                'has_next' => true,
                'next_cursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => 1],
            ],
            Post::lampager()
                ->backward()->limit(3)
                ->orderByDesc('updated_at')
                ->orderByDesc('id')
                ->seekable()
                ->paginate(['id' => 3, 'updated_at' => '2017-01-01 10:00:00'])
        );
    }

    #[Test]
    public function testDescendingBackwardExclusive(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => 4, 'updated_at' => EloquentDate::format('2017-01-01 11:00:00')],
                    ['id' => 2, 'updated_at' => EloquentDate::format('2017-01-01 11:00:00')],
                    ['id' => 5, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                ],
                'has_previous' => false,
                'previous_cursor' => null,
                'has_next' => true,
                'next_cursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => 5],
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

    #[Test]
    public function testBelongsToManyOrderByPivot(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => 3, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                    ['id' => 5, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                    ['id' => 2, 'updated_at' => EloquentDate::format('2017-01-01 11:00:00')],
                ],
                'has_previous' => true,
                'previous_cursor' => ['pivot_id' => 1],
                'has_next' => true,
                'next_cursor' => ['pivot_id' => 5],
            ],
            Tag::find(1)->posts()->withPivot('id')
                ->lampager()
                ->forward()->limit(3)
                ->orderBy('pivot_id')
                ->seekable()
                ->paginate(['pivot_id' => 2])
        );
    }

    #[Test]
    public function testBelongsToManyOrderBySource(): void
    {
        $this->assertResultSame(
            [
                'records' => [
                    ['id' => 2, 'updated_at' => EloquentDate::format('2017-01-01 11:00:00')],
                    ['id' => 3, 'updated_at' => EloquentDate::format('2017-01-01 10:00:00')],
                    ['id' => 4, 'updated_at' => EloquentDate::format('2017-01-01 11:00:00')],
                ],
                'has_previous' => true,
                'previous_cursor' => ['posts.id' => 1],
                'has_next' => true,
                'next_cursor' => ['posts.id' => 5],
            ],
            Tag::find(1)->posts()->withPivot('id')
                ->lampager()
                ->forward()->limit(3)
                ->orderBy('posts.id')
                ->seekable()
                ->paginate(['posts.id' => 2])
        );
    }

    #[Test]
    public function testBelongsToManyWithoutPivotKey(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The column `id` is not included in the pivot "pivot".');

        Tag::find(1)->posts()
            ->lampager()
            ->forward()->limit(3)
            ->orderBy('pivot_id')
            ->seekable()
            ->paginate();
    }
}
