<p align="center">
<img width="320" alt="lampager-laravel" src="https://user-images.githubusercontent.com/1351893/31755018-9ab0c8ae-b4d6-11e7-9310-dbc372998ee4.png">
</p>
<p align="center">
<a href="https://travis-ci.org/lampager/lampager-laravel"><img src="https://travis-ci.org/lampager/lampager-laravel.svg?branch=master" alt="Build Status"></a>
<a href="https://coveralls.io/github/lampager/lampager-laravel?branch=master"><img src="https://coveralls.io/repos/github/lampager/lampager-laravel/badge.svg?branch=master" alt="Coverage Status"></a>
<a href="https://scrutinizer-ci.com/g/lampager/lampager-laravel/?branch=master"><img src="https://scrutinizer-ci.com/g/lampager/lampager-laravel/badges/quality-score.png?b=master" alt="Scrutinizer Code Quality"></a>
</p>

# Lampager for Laravel

Rapid pagination without using OFFSET

## Requirements

- PHP: ^5.6 || ^7.0
- Laravel: ^5.4
- [lampager/lampager](https://github.com/lampager/lampager): ^0.1

## Installing

```bash
composer require lampager/lampager-laravel:^0.1.0
```

## Basic Usage

Register service provider.

`config/app.php`:

```php
        /*
         * Package Service Providers...
         */
        mpyw\Lampage\Provider\IlluminateMacroServiceProvider::class,
```

Then you can chain `->lampager()` method from Query Builder, Eloquent Builder and Relation.

```php
$cursor = [
    'id' => 3,
    'created_at' => '2017-01-10 00:00:00',
    'updated_at' => '2017-01-20 00:00:00',
];

$result = App\Post::whereUserId(1)
    ->lampager()
    ->forward()
    ->limit(5)
    ->orderByDesc('updated_at') // ORDER BY `updated_at` DESC, `created_at` DESC, `id` DESC
    ->orderByDesc('created_at')
    ->orderByDesc('id')
    ->seekable()
    ->paginate($cursor);
```

It will run the optimized query.


```sql
(

    SELECT * FROM `posts`
    WHERE `user_id` = 1
    AND (
        `updated_at` = '2017-01-20 00:00:00' AND `created_at` = '2017-01-10 00:00:00' AND `id` > 3
        OR
        `updated_at` = '2017-01-20 00:00:00' AND `created_at` > '2017-01-10 00:00:00'
        OR
        `updated_at` > '2017-01-20 00:00:00'
    )
    ORDER BY `updated_at` ASC, `created_at` ASC, `id` ASC
    LIMIT 1

) UNION ALL (

    SELECT * FROM `posts`
    WHERE `user_id` = 1
    AND (
        `updated_at` = '2017-01-20 00:00:00' AND `created_at` = '2017-01-10 00:00:00' AND `id` <= 3
        OR
        `updated_at` = '2017-01-20 00:00:00' AND `created_at` < '2017-01-10 00:00:00'
        OR
        `updated_at` < '2017-01-20 00:00:00'
    )
    ORDER BY `updated_at` DESC, `created_at` DESC, `id` DESC
    LIMIT 4

)
```

And you'll get


```json
{
  "records": [
    {
      "id": 3,
      "user_id": 1,
      "text": "foo",
      "created_at": "2017-01-10 00:00:00",
      "updated_at": "2017-01-20 00:00:00"
    },
    {
      "id": 5,
      "user_id": 1,
      "text": "bar",
      "created_at": "2017-01-05 00:00:00",
      "updated_at": "2017-01-20 00:00:00"
    },
    {
      "id": 4,
      "user_id": 1,
      "text": "baz",
      "created_at": "2017-01-05 00:00:00",
      "updated_at": "2017-01-20 00:00:00"
    },
    {
      "id": 2,
      "user_id": 1,
      "text": "qux",
      "created_at": "2017-01-17 00:00:00",
      "updated_at": "2017-01-18 00:00:00"
    },
    {
      "id": 1,
      "user_id": 1,
      "text": "quux",
      "created_at": "2017-01-16 00:00:00",
      "updated_at": "2017-01-18 00:00:00"
    }
  ],
  "meta": {
    "previous_cursor": null,
    "next_cursor": {
      "id": 6,
      "created_at": "2017-01-14 00:00:00",
      "updated_at": "2017-01-18 00:00:00"
    }
  }
}
```

## Classes

Note: See also [lampager/lampager](https://github.com/lampager/lampager).

| Name | Type | Parent Class | Description |
|:---|:---|:---|:---|
| Lampager\\Laravel\\`Paginator` | Class | Lampager\\`Paginator` | Fluent factory implementation for Laravel |
| Lampager\\Laravel\\`Processor` | Class | Lampager\\`AbstractProcessor` | Processor implementation for Laravel |
| Lampager\\Laravel\\`MacroServiceProvider` | Class | Illuminate\\Support\\`ServiceProvider` | Enable macros chainable from QueryBuilder, ElqouentBuilder and Relation |

## API

Note: See also [lampager/lampager](https://github.com/lampager/lampager).

### Paginator::__construct()<br>Paginator::create()

Create a new paginator instance.  
If you use Laravel macros, however, you don't need to directly instantiate.

```php
static Paginator create(QueryBuilder|EloquentBuilder|Relation $builder): static
Paginator::__construct(QueryBuilder|EloquentBuilder|Relation $builder)
```

- `QueryBuilder` means `\Illuminate\Database\Query\Builder`
- `EloquentBuilder` means `\Illuminate\Database\Eloquent\Builder`
- `Relation` means `\Illuminate\Database\Eloquent\Relation`

### Paginator::transform()

Transform Lampager Query into Illuminate builder.

```php
Paginator::transform(Query $query): QueryBuilder|EloquentBuilder|Relation
```

### Paginator::build()

Perform configure + transform.

```php
Paginator::build(\Lampager\Cursor|array $cursor = []): QueryBuilder|EloquentBuilder|Relation
```

### Paginator::paginate()

Perform configure + transform + process.

```php
Paginator::paginate(\Lampager\Cursor|array $cursor = []): \Illuminate\Support\Collection
```

#### Arguments

- **`(array)`** __*$cursor*__<br> Associative array that contains `$column => $value`. It must be **all-or-nothing**.
  - For initial page, omit this parameter or pass empty array.
  - For subsequent pages, pass all parameters. Partial parameters are not allowd.

#### Return Value

Default format when using `Illuminate\Database\Eloquent\Builder`:

```php
new \Illuminate\Support\Collection([
    'records' => new \Illuminate\Database\Eloquent\Collection([
        new \Illuminate\Database\Eloquent\Model([...]),
        new \Illuminate\Database\Eloquent\Model([...]),
        new \Illuminate\Database\Eloquent\Model([...]),
        ...,
    ]),
    'meta' => new \Illuminate\Support\Collection([
        // IMPORTANT: Either of cursor does not exist when UNION ALL query is not executed.
        'previous_cursor' => null,
        'next_cursor => [
            'updated_at' => '2017-01-01 00:02:00',
            'created_at' => '2017-01-01 00:01:00',
            'id' => 1,
        ],
    ])
])
```

### Paginator::useFormatter()<br>Paginator::restoreFormatter()<br>Paginator::process()

Invoke Processor methods.

```php
Paginator::useFormatter(Formatter|callable $formatter): $this
Paginator::restoreFormatter(): $this
Paginator::process(\Lampager\Query $query, \Illuminate\Database\Eloquent\Collection $rows): \Illuminate\Support\Collection
```
