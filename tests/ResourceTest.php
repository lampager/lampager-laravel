<?php

namespace Lampager\Laravel\Tests;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use PHPUnit\Framework\Attributes\Test;

class ResourceTest extends TestCase
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

    /**
     * @return \Lampager\Laravel\PaginationResult
     */
    protected function getLampagerPagination()
    {
        return Post::lampager()
            ->forward()->limit(3)
            ->orderBy('updated_at')
            ->orderBy('id')
            ->seekable()
            ->paginate(['id' => 3, 'updated_at' => '2017-01-01 10:00:00']);
    }

    /**
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    protected function getStandardPagination()
    {
        return Post::query()
            ->where('id', '>', 1)
            ->orderBy('updated_at')
            ->orderBy('id')
            ->simplePaginate(3);
    }

    #[Test]
    public function testRawArrayOutput(): void
    {
        $expected = [
            [
                'id' => 3,
                'updated_at' => EloquentDate::format('2017-01-01 10:00:00'),
                'post_resource' => true,
            ],
            [
                'id' => 5,
                'updated_at' => EloquentDate::format('2017-01-01 10:00:00'),
                'post_resource' => true,
            ],
            [
                'id' => 2,
                'updated_at' => EloquentDate::format('2017-01-01 11:00:00'),
                'post_resource' => true,
            ],
        ];

        $pagination = $this->getLampagerPagination();
        $records = $pagination->records;
        $standardPagination = $this->getStandardPagination();

        $this->assertResultSame($expected, (new PostResourceCollection($pagination))->resolve());
        $this->assertResultSame($expected, (new PostResourceCollection($records))->resolve());
        $this->assertResultSame($expected, (new PostResourceCollection($standardPagination))->resolve());
    }

    #[Test]
    public function testStructuredArrayOutput(): void
    {
        // Since Laravel 11, resolve() calls toAttributes() which returns
        // the collection items directly, bypassing toArray() structure
        $expectedResolved = [
            [
                'id' => 3,
                'updated_at' => EloquentDate::format('2017-01-01 10:00:00'),
                'post_resource' => true,
            ],
            [
                'id' => 5,
                'updated_at' => EloquentDate::format('2017-01-01 10:00:00'),
                'post_resource' => true,
            ],
            [
                'id' => 2,
                'updated_at' => EloquentDate::format('2017-01-01 11:00:00'),
                'post_resource' => true,
            ],
        ];

        $expectedResponse = [
            'data' => $expectedResolved,
            'post_resource_collection' => true,
        ];

        $pagination = $this->getLampagerPagination();
        $records = $pagination->records;
        $standardPagination = $this->getStandardPagination();

        // resolve() now returns collection items directly (Laravel 11+ behavior)
        $this->assertResultSame($expectedResolved, (new StructuredPostResourceCollection($pagination))->resolve());
        $this->assertResultSame($expectedResolved, (new StructuredPostResourceCollection($records))->resolve());
        $this->assertResultSame($expectedResolved, (new StructuredPostResourceCollection($standardPagination))->resolve());

        // toResponse() still uses toArray() structure
        $this->assertResultSame($expectedResponse, (new StructuredPostResourceCollection($records))
            ->toResponse(Request::create('/'))->getData()
        );
        $this->assertResultSame($expectedResponse, (new PostResourceCollection($records))
            ->additional(['post_resource_collection' => true])
            ->toResponse(Request::create('/'))->getData()
        );
    }

    #[Test]
    public function testLampagerPaginationOutput(): void
    {
        // with() data is merged at the end
        $expected = [
            'data' => [
                [
                    'id' => 3,
                    'updated_at' => EloquentDate::format('2017-01-01 10:00:00'),
                    'post_resource' => true,
                ],
                [
                    'id' => 5,
                    'updated_at' => EloquentDate::format('2017-01-01 10:00:00'),
                    'post_resource' => true,
                ],
                [
                    'id' => 2,
                    'updated_at' => EloquentDate::format('2017-01-01 11:00:00'),
                    'post_resource' => true,
                ],
            ],
            'has_previous' => true,
            'previous_cursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => 1],
            'has_next' => true,
            'next_cursor' => ['updated_at' => '2017-01-01 11:00:00', 'id' => 4],
            'post_resource_collection' => true,
        ];

        $pagination = $this->getLampagerPagination();

        $this->assertResultSame($expected, (new StructuredPostResourceCollection($pagination))
            ->toResponse(Request::create('/'))->getData()
        );
        $this->assertResultSame($expected, (new PostResourceCollection($pagination))
            ->additional(['post_resource_collection' => true])
            ->toResponse(Request::create('/'))->getData()
        );
    }

    #[Test]
    public function testStandardPaginationOutput(): void
    {
        // with() data is merged at the end, current_page_url added in Laravel 11+
        $expected = [
            'data' => [
                [
                    'id' => 3,
                    'updated_at' => EloquentDate::format('2017-01-01 10:00:00'),
                    'post_resource' => true,
                ],
                [
                    'id' => 5,
                    'updated_at' => EloquentDate::format('2017-01-01 10:00:00'),
                    'post_resource' => true,
                ],
                [
                    'id' => 2,
                    'updated_at' => EloquentDate::format('2017-01-01 11:00:00'),
                    'post_resource' => true,
                ],
            ],
            'links' => [
                'first' => 'http://localhost?page=1',
                'last' => null,
                'prev' => null,
                'next' => 'http://localhost?page=2',
            ],
            'meta' => [
                'current_page' => 1,
                'current_page_url' => 'http://localhost?page=1',
                'from' => 1,
                'path' => 'http://localhost',
                'per_page' => 3,
                'to' => 3,
            ],
            'post_resource_collection' => true,
        ];

        $pagination = $this->getStandardPagination();

        $this->assertResultSame($expected, (new StructuredPostResourceCollection($pagination))
            ->toResponse(Request::create('/'))->getData()
        );
        $this->assertResultSame($expected, (new PostResourceCollection($pagination))
            ->additional(['post_resource_collection' => true])
            ->toResponse(Request::create('/'))->getData()
        );
    }

    #[Test]
    public function testMissingValue(): void
    {
        $expected = ['id' => 1];
        $actual = (new TagResource(Tag::find(1)))->resolve();

        $this->assertResultSame($expected, $actual);
    }

    #[Test]
    public function testAnonymousResourceCollection(): void
    {
        $collection = PostResource::collection($this->getLampagerPagination());
        $this->assertInstanceOf(AnonymousResourceCollection::class, $collection);

        $expected = [
            'data' => [
                [
                    'id' => 3,
                    'updated_at' => EloquentDate::format('2017-01-01 10:00:00'),
                    'post_resource' => true,
                ],
                [
                    'id' => 5,
                    'updated_at' => EloquentDate::format('2017-01-01 10:00:00'),
                    'post_resource' => true,
                ],
                [
                    'id' => 2,
                    'updated_at' => EloquentDate::format('2017-01-01 11:00:00'),
                    'post_resource' => true,
                ],
            ],
            'has_previous' => true,
            'previous_cursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => 1],
            'has_next' => true,
            'next_cursor' => ['updated_at' => '2017-01-01 11:00:00', 'id' => 4],
        ];
        $this->assertResultSame($expected, $collection->toResponse(Request::create('/'))->getData());
    }
}
