<?php

namespace Lampager\Laravel\Tests;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Arr;

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

    /**
     * @test
     */
    public function testRawArrayOutput()
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

    /**
     * @test
     */
    public function testStructuredArrayOutput()
    {
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
            'post_resource_collection' => true,
        ];

        $pagination = $this->getLampagerPagination();
        $records = $pagination->records;
        $standardPagination = $this->getStandardPagination();

        $this->assertResultSame($expected, (new StructuredPostResourceCollection($pagination))->resolve());
        $this->assertResultSame($expected, (new StructuredPostResourceCollection($records))->resolve());
        $this->assertResultSame($expected, (new StructuredPostResourceCollection($standardPagination))->resolve());

        $this->assertResultSame($expected, (new StructuredPostResourceCollection($records))
            ->toResponse(null)->getData()
        );
        $this->assertResultSame($expected, (new PostResourceCollection($records))
            ->additional(['post_resource_collection' => true])
            ->toResponse(null)->getData()
        );
    }

    /**
     * @test
     */
    public function testLampagerPaginationOutput()
    {
        $expected1 = [
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
            'post_resource_collection' => true,
            'has_previous' => true,
            'previous_cursor' => ['updated_at' => '2017-01-01 10:00:00', 'id' => 1],
            'has_next' => true,
            'next_cursor' => ['updated_at' => '2017-01-01 11:00:00', 'id' => 4],
        ];
        // different order
        $expected2 = Arr::except($expected1, 'post_resource_collection') + ['post_resource_collection' => true];

        $pagination = $this->getLampagerPagination();

        $this->assertResultSame($expected1, (new StructuredPostResourceCollection($pagination))
            ->toResponse(null)->getData()
        );
        $this->assertResultSame($expected2, (new PostResourceCollection($pagination))
            ->additional(['post_resource_collection' => true])
            ->toResponse(null)->getData()
        );
    }

    /**
     * @test
     */
    public function testStandardPaginationOutput()
    {
        $expected1 = [
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
            'post_resource_collection' => true,
            'links' => [
                'first' => 'http://localhost?page=1',
                'last' => null,
                'prev' => null,
                'next' => 'http://localhost?page=2',
            ],
            'meta' => [
                'current_page' => 1,
                'from' => 1,
                'path' => 'http://localhost',
                'per_page' => 3,
                'to' => 3,
            ],
        ];
        // different order
        $expected2 = Arr::except($expected1, 'post_resource_collection') + ['post_resource_collection' => true];

        $pagination = $this->getStandardPagination();

        $this->assertResultSame($expected1, (new StructuredPostResourceCollection($pagination))
            ->toResponse(null)->getData()
        );
        $this->assertResultSame($expected2, (new PostResourceCollection($pagination))
            ->additional(['post_resource_collection' => true])
            ->toResponse(null)->getData()
        );
    }

    /**
     * @test
     */
    public function testMissingValue()
    {
        $expected = ['id' => 1];
        $actual = (new TagResource(Tag::find(1)))->resolve();

        $this->assertResultSame($expected, $actual);
    }

    /**
     * @test
     */
    public function testAnonymousResourceCollection()
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
        $this->assertResultSame($expected, $collection->toResponse(null)->getData());
    }
}
