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
- [lampager/lampager](https://github.com/lampager/lampager): ^0.3

## Installing

```bash
composer require lampager/lampager-laravel:^0.2.0
```

## Basic Usage

Register service provider.

`config/app.php`:

```php
        /*
         * Package Service Providers...
         */
        Lampager\Laravel\MacroServiceProvider::class,
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
    ->paginate($cursor)
    ->toJson(JSON_PRETTY_PRINT);
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
    LIMIT 6

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
  "has_previous": false,
  "previous_cursor": null,
  "has_next": true,
  "next_cursor": {
    "updated_at": "2017-01-18 00:00:00",
    "created_at": "2017-01-14 00:00:00",
    "id": 6
  }
}
```

### Attention

Do not use eager loading constraints on `* to many` relations. Those query confuses Lampager.

```php
App\Post::with('comments')->lampager() // BAD (post has many comments)
App\Post::with('user')->lampager() // GOOD (post belongs to user)
```

## Classes

Note: See also [lampager/lampager](https://github.com/lampager/lampager).

| Name | Type | Parent Class | Description |
|:---|:---|:---|:---|
| Lampager\\Laravel\\`Paginator` | Class | Lampager\\`Paginator` | Fluent factory implementation for Laravel |
| Lampager\\Laravel\\`Processor` | Class | Lampager\\`AbstractProcessor` | Processor implementation for Laravel |
| Lampager\\Laravel\\`PaginationResult` | Class | Lampager\\`PaginationResult` | PaginationResult implementation for Laravel |
| Lampager\\Laravel\\`MacroServiceProvider` | Class | Illuminate\\Support\\`ServiceProvider` | Enable macros chainable from QueryBuilder, ElqouentBuilder and Relation |

`Paginator`, `Processor` and `PaginationResult` are macroable.

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
Paginator::paginate(\Lampager\Cursor|array $cursor = []): \Lampager\Laravel\PaginationResult
```

#### Arguments

- **`(mixed)`** __*$cursor*__<br> An associative array that contains `$column => $value` or an object that implements `\Lampager\Cursor`. It must be **all-or-nothing**.
  - For initial page, omit this parameter or pass empty array.
  - For subsequent pages, pass all parameters. Partial parameters are not allowd.

#### Return Value

e.g. 

(Default format when using `\Illuminate\Database\Eloquent\Builder`)

```php
object(Lampager\Laravel\PaginationResult)#1 (5) {
  ["records"]=>
  object(Illuminate\Database\Eloquent\Collection)#2 (1) {
    ["items":protected]=>
    array(5) {
      [0]=>
      object(App\Post)#2 (26) { ... }
      [1]=>
      object(App\Post)#3 (26) { ... }
      [2]=>
      object(App\Post)#4 (26) { ... }
      [3]=>
      object(App\Post)#5 (26) { ... }
      [4]=>
      object(App\Post)#6 (26) { ... }
    }
  }
  ["hasPrevious"]=>
  bool(false)
  ["previousCursor"]=>
  NULL
  ["hasNext"]=>
  bool(true)
  ["nextCursor"]=>
  array(2) {
    ["updated_at"]=>
    string(19) "2017-01-18 00:00:00"
    ["created_at"]=>
    string(19) "2017-01-14 00:00:00"
    ["id"]=>
    int(6)
  }
}
```

### Paginator::useFormatter()<br>Paginator::restoreFormatter()<br>Paginator::process()

Invoke Processor methods.

```php
Paginator::useFormatter(Formatter|callable $formatter): $this
Paginator::restoreFormatter(): $this
Paginator::process(\Lampager\Query $query, \Illuminate\Support\Collection|\Illuminate\Database\Eloquent\Collection $rows): \Lampager\Laravel\PaginationResult
```

### PaginationResult::toArray()<br>PaginationResult::jsonSerialize()

Convert the object into array.

**IMPORTANT: `camelCase` properties are converted into `snake_case` form.**

```php
PaginationResult::toArray(): array
PaginationResult::jsonSerialize(): array
```

### PaginationResult::__call()

Call macro or Collection methods.

```php
PaginationResult::__call(string $name, array $args): mixed
```

e.g.

```php
PaginationResult::macro('foo', function () {
    return ...;
});
$foo = $result->foo();
```

```php
$first = $result->first();
```
