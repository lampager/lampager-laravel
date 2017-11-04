<?php

namespace Lampager\Laravel\Tests;

use Lampager\Laravel\PaginationResult;

class PaginationResultTest extends TestCase
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
     * @test
     */
    public function testMacroCall()
    {
        PaginationResult::macro('meta', function () {
            $vars = $this->toArray();
            unset($vars['records']);
            return $vars;
        });

        $this->assertResultSame(
            [
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
                ->meta()
        );
    }

    /**
     * @test
     */
    public function testCollectionCall()
    {
        $result = Post::lampager()
            ->forward()->limit(3)
            ->orderBy('updated_at')
            ->orderBy('id')
            ->seekable()
            ->paginate(['id' => 3, 'updated_at' => '2017-01-01 10:00:00']);

        $this->assertResultSame(
            ['id' => 3, 'updated_at' => '2017-01-01 10:00:00'],
            $result->first()
        );
    }

    /**
     * @test
     */
    public function testJsonEncodeWithOption()
    {
        $actual = Post::lampager()
            ->forward()->limit(3)
            ->orderBy('updated_at')
            ->orderBy('id')
            ->seekable()
            ->paginate(['id' => 3, 'updated_at' => '2017-01-01 10:00:00'])
            ->toJson(JSON_PRETTY_PRINT);

        $expected = <<<'EOD'
{
    "records": [
        {
            "id": 3,
            "updated_at": "2017-01-01 10:00:00"
        },
        {
            "id": 5,
            "updated_at": "2017-01-01 10:00:00"
        },
        {
            "id": 2,
            "updated_at": "2017-01-01 11:00:00"
        }
    ],
    "has_previous": true,
    "previous_cursor": {
        "updated_at": "2017-01-01 10:00:00",
        "id": 1
    },
    "has_next": true,
    "next_cursor": {
        "updated_at": "2017-01-01 11:00:00",
        "id": 4
    }
}
EOD;
        $this->assertSame($expected, $actual);
    }
}
