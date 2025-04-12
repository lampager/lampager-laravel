<?php

namespace Lampager\Laravel\Tests;

use NilPortugues\Sql\QueryFormatter\Formatter;
use Orchestra\Testbench\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\Test;

class MySQLGrammarTest extends TestCase
{
    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'mysql');
    }

    protected function setUp(): void
    {
        BaseTestCase::setUp();
    }

    /**
     * @param $expected
     * @param $actual
     */
    protected function assertSqlEquals($expected, $actual): void
    {
        $formatter = new Formatter();
        $this->assertEquals($formatter->format($expected), $formatter->format($actual));
    }

    #[Test]
    public function testAscendingForwardStart(): void
    {
        $builder = Post::whereUserId(2)
            ->lampager()
            ->forward()->limit(3)
            ->orderBy('updated_at')
            ->orderBy('created_at')
            ->orderBy('id')
            ->seekable()
            ->build();
        $this->assertSqlEquals('
            select * from `posts`
            where `user_id` = ? 
            order by `updated_at` asc, `created_at` asc, `id` asc
            limit 4
        ', $builder->toSql());
    }

    #[Test]
    public function testAscendingForwardInclusive(): void
    {
        $cursor = ['updated_at' => '', 'created_at' => '', 'id' => ''];
        $builder = Post::whereUserId(2)
            ->lampager()
            ->forward()->limit(3)
            ->orderBy('updated_at')
            ->orderBy('created_at')
            ->orderBy('id')
            ->seekable()
            ->build($cursor);
        $this->assertSqlEquals('
            (
                select * from `posts`
                where `user_id` = ? AND (
                  `updated_at` = ? AND `created_at` = ? AND `id` < ? OR 
                  `updated_at` = ? AND `created_at` < ? OR 
                  `updated_at` < ?
                )
                order by `updated_at` desc, `created_at` desc, `id` desc
                limit 1
            )
            union all
            (
                select * from `posts`
                where `user_id` = ? AND (
                  `updated_at` = ? AND `created_at` = ? AND `id` >= ? OR 
                  `updated_at` = ? AND `created_at` > ? OR 
                  `updated_at` > ?
                )
                order by `updated_at` asc, `created_at` asc, `id` asc
                limit 4
            )
        ', $builder->toSql());
    }

    #[Test]
    public function testAscendingForwardExclusive(): void
    {
        $cursor = ['updated_at' => '', 'created_at' => '', 'id' => ''];
        $builder = Post::whereUserId(2)
            ->lampager()
            ->forward()->limit(3)
            ->orderBy('updated_at')
            ->orderBy('created_at')
            ->orderBy('id')
            ->seekable()
            ->exclusive()
            ->build($cursor);
        $this->assertSqlEquals('
            (
                select * from `posts`
                where `user_id` = ? AND (
                  `updated_at` = ? AND `created_at` = ? AND `id` <= ? OR 
                  `updated_at` = ? AND `created_at` < ? OR 
                  `updated_at` < ?
                )
                order by `updated_at` desc, `created_at` desc, `id` desc
                limit 1
            )
            union all
            (
                select * from `posts`
                where `user_id` = ? AND (
                  `updated_at` = ? AND `created_at` = ? AND `id` > ? OR 
                  `updated_at` = ? AND `created_at` > ? OR 
                  `updated_at` > ?
                )
                order by `updated_at` asc, `created_at` asc, `id` asc
                limit 4
            )
        ', $builder->toSql());
    }

    #[Test]
    public function testAscendingBackwardStart(): void
    {
        $builder = Post::whereUserId(2)
            ->lampager()
            ->backward()->limit(3)
            ->orderBy('updated_at')
            ->orderBy('created_at')
            ->orderBy('id')
            ->seekable()
            ->build();
        $this->assertSqlEquals('
            select * from `posts`
            where `user_id` = ?
            order by `updated_at` desc, `created_at` desc, `id` desc
            limit 4
        ', $builder->toSql());
    }

    #[Test]
    public function testAscendingBackwardInclusive(): void
    {
        $cursor = ['updated_at' => '', 'created_at' => '', 'id' => ''];
        $builder = Post::whereUserId(2)
            ->lampager()
            ->backward()->limit(3)
            ->orderBy('updated_at')
            ->orderBy('created_at')
            ->orderBy('id')
            ->seekable()
            ->build($cursor);
        $this->assertSqlEquals('
            (
                select * from `posts`
                where `user_id` = ? AND (
                  `updated_at` = ? AND `created_at` = ? AND `id` > ? OR 
                  `updated_at` = ? AND `created_at` > ? OR 
                  `updated_at` > ?
                )
                order by `updated_at` asc, `created_at` asc, `id` asc
                limit 1
            )
            union all
            (
                select * from `posts`
                where `user_id` = ? AND (
                  `updated_at` = ? AND `created_at` = ? AND `id` <= ? OR 
                  `updated_at` = ? AND `created_at` < ? OR 
                  `updated_at` < ?
                )
                order by `updated_at` desc, `created_at` desc, `id` desc
                limit 4
            )
        ', $builder->toSql());
    }

    #[Test]
    public function testAscendingBackwardExclusive(): void
    {
        $cursor = ['updated_at' => '', 'created_at' => '', 'id' => ''];
        $builder = Post::whereUserId(2)
            ->lampager()
            ->backward()->limit(3)
            ->orderBy('updated_at')
            ->orderBy('created_at')
            ->orderBy('id')
            ->seekable()
            ->exclusive()
            ->build($cursor);
        $this->assertSqlEquals('
            (
                select * from `posts`
                where `user_id` = ? AND (
                  `updated_at` = ? AND `created_at` = ? AND `id` >= ? OR 
                  `updated_at` = ? AND `created_at` > ? OR 
                  `updated_at` > ?
                )
                order by `updated_at` asc, `created_at` asc, `id` asc
                limit 1
            )
            union all
            (
                select * from `posts`
                where `user_id` = ? AND (
                  `updated_at` = ? AND `created_at` = ? AND `id` < ? OR 
                  `updated_at` = ? AND `created_at` < ? OR 
                  `updated_at` < ?
                )
                order by `updated_at` desc, `created_at` desc, `id` desc
                limit 4
            )
        ', $builder->toSql());
    }

    #[Test]
    public function testDescendingForwardStart(): void
    {
        $builder = Post::whereUserId(2)
            ->lampager()
            ->forward()->limit(3)
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->seekable()
            ->build();
        $this->assertSqlEquals('
            select * from `posts`
            where `user_id` = ?
            order by `updated_at` desc, `created_at` desc, `id` desc
            limit 4
        ', $builder->toSql());
    }

    #[Test]
    public function testDescendingForwardInclusive(): void
    {
        $cursor = ['updated_at' => '', 'created_at' => '', 'id' => ''];
        $builder = Post::whereUserId(2)
            ->lampager()
            ->forward()->limit(3)
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->seekable()
            ->build($cursor);
        $this->assertSqlEquals('
            (
                select * from `posts`
                where `user_id` = ? AND (
                  `updated_at` = ? AND `created_at` = ? AND `id` > ? OR 
                  `updated_at` = ? AND `created_at` > ? OR 
                  `updated_at` > ?
                )
                order by `updated_at` asc, `created_at` asc, `id` asc
                limit 1
            )
            union all
            (
                select * from `posts`
                where `user_id` = ? AND (
                  `updated_at` = ? AND `created_at` = ? AND `id` <= ? OR 
                  `updated_at` = ? AND `created_at` < ? OR 
                  `updated_at` < ?
                )
                order by `updated_at` desc, `created_at` desc, `id` desc
                limit 4
            )
        ', $builder->toSql());
    }

    #[Test]
    public function testDescendingForwardExclusive(): void
    {
        $cursor = ['updated_at' => '', 'created_at' => '', 'id' => ''];
        $builder = Post::whereUserId(2)
            ->lampager()
            ->forward()->limit(3)
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->seekable()
            ->exclusive()
            ->build($cursor);
        $this->assertSqlEquals('
            (
                select * from `posts`
                where `user_id` = ? AND (
                  `updated_at` = ? AND `created_at` = ? AND `id` >= ? OR 
                  `updated_at` = ? AND `created_at` > ? OR 
                  `updated_at` > ?
                )
                order by `updated_at` asc, `created_at` asc, `id` asc
                limit 1
            )
            union all
            (
                select * from `posts`
                where `user_id` = ? AND (
                  `updated_at` = ? AND `created_at` = ? AND `id` < ? OR 
                  `updated_at` = ? AND `created_at` < ? OR 
                  `updated_at` < ?
                )
                order by `updated_at` desc, `created_at` desc, `id` desc
                limit 4
            )
        ', $builder->toSql());
    }

    #[Test]
    public function testDescendingBackwardStart(): void
    {
        $builder = Post::whereUserId(2)
            ->lampager()
            ->backward()->limit(3)
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->seekable()
            ->build();
        $this->assertSqlEquals('
            select * from `posts`
            where `user_id` = ?
            order by `updated_at` asc, `created_at` asc, `id` asc
            limit 4
        ', $builder->toSql());
    }

    #[Test]
    public function testDescendingBackwardInclusive(): void
    {
        $cursor = ['updated_at' => '', 'created_at' => '', 'id' => ''];
        $builder = Post::whereUserId(2)
            ->lampager()
            ->backward()->limit(3)
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->seekable()
            ->build($cursor);
        $this->assertSqlEquals('
            (
                select * from `posts`
                where `user_id` = ? AND (
                  `updated_at` = ? AND `created_at` = ? AND `id` < ? OR 
                  `updated_at` = ? AND `created_at` < ? OR 
                  `updated_at` < ?
                )
                order by `updated_at` desc, `created_at` desc, `id` desc
                limit 1
            )
            union all
            (
                select * from `posts`
                where `user_id` = ? AND (
                  `updated_at` = ? AND `created_at` = ? AND `id` >= ? OR 
                  `updated_at` = ? AND `created_at` > ? OR 
                  `updated_at` > ?
                )
                order by `updated_at` asc, `created_at` asc, `id` asc
                limit 4
            )
        ', $builder->toSql());
    }

    #[Test]
    public function testDescendingBackwardExclusive(): void
    {
        $cursor = ['updated_at' => '', 'created_at' => '', 'id' => ''];
        $builder = Post::whereUserId(2)
            ->lampager()
            ->backward()->limit(3)
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->seekable()
            ->exclusive()
            ->build($cursor);
        $this->assertSqlEquals('
            (
                select * from `posts`
                where `user_id` = ? AND (
                  `updated_at` = ? AND `created_at` = ? AND `id` <= ? OR 
                  `updated_at` = ? AND `created_at` < ? OR 
                  `updated_at` < ?
                )
                order by `updated_at` desc, `created_at` desc, `id` desc
                limit 1
            )
            union all
            (
                select * from `posts`
                where `user_id` = ? AND (
                  `updated_at` = ? AND `created_at` = ? AND `id` > ? OR 
                  `updated_at` = ? AND `created_at` > ? OR 
                  `updated_at` > ?
                )
                order by `updated_at` asc, `created_at` asc, `id` asc
                limit 4
            )
        ', $builder->toSql());
    }

    #[Test]
    public function testBelongsToManyOrderByPivot(): void
    {
        $cursor = ['pivot_id' => 2];

        $tag = new Tag();
        $tag->id = 1;
        $tag->exists = true;

        $builder = $tag->posts()->withPivot('id')
            ->lampager()
            ->forward()->limit(3)
            ->orderBy('pivot_id')
            ->seekable()
            ->build($cursor);

        $this->assertSqlEquals('
            (
                select * from `posts`
                inner join `post_tag` on `posts`.`id` = `post_tag`.`post_id`
                where `post_tag`.`tag_id` = ? AND (
                    `post_tag`.`id` < ?
                )
                order by `pivot_id` desc
                limit 1
            )
            union all
            (
                select
                    `posts`.*,
                    `post_tag`.`tag_id` as `pivot_tag_id`,
                    `post_tag`.`post_id` as `pivot_post_id`,
                    `post_tag`.`id` as `pivot_id`
                from `posts`
                inner join `post_tag` on `posts`.`id` = `post_tag`.`post_id`
                where `post_tag`.`tag_id` = ? AND (
                    `post_tag`.`id` >= ?
                )
                order by `pivot_id` asc
                limit 4
            )
        ', $builder->toSql());
    }

    #[Test]
    public function testBelongsToManyOrderBySource(): void
    {
        $cursor = ['posts.id' => 2];

        $tag = new Tag();
        $tag->id = 1;
        $tag->exists = true;

        $builder = $tag->posts()->withPivot('id')
            ->lampager()
            ->forward()->limit(3)
            ->orderBy('posts.id')
            ->seekable()
            ->build($cursor);

        $this->assertSqlEquals('
            (
                select * from `posts`
                inner join `post_tag` on `posts`.`id` = `post_tag`.`post_id`
                where `post_tag`.`tag_id` = ? AND (
                    `posts`.`id` < ?
                )
                order by `posts`.`id` desc
                limit 1
            )
            union all
            (
                select
                    `posts`.*,
                    `post_tag`.`tag_id` as `pivot_tag_id`,
                    `post_tag`.`post_id` as `pivot_post_id`,
                    `post_tag`.`id` as `pivot_id`
                from `posts`
                inner join `post_tag` on `posts`.`id` = `post_tag`.`post_id`
                where `post_tag`.`tag_id` = ? AND (
                    `posts`.`id` >= ?
                )
                order by `posts`.`id` asc
                limit 4
            )
        ', $builder->toSql());
    }
}
