<?php

namespace Lampager\Laravel\Tests;

use Illuminate\Database\Eloquent\Model;
use Lampager\Laravel\Paginator;

/**
 * Class Post
 *
 * @method static Paginator lampager()
 * @method static Post create(array $attributes = [])
 * @method static Post whereUserId(int $userId)
 */
class Post extends Model
{
    protected $fillable = ['id', 'updated_at'];

    public $timestamps = false;

    protected $hidden = ['pivot'];

    protected $casts = [
        'id' => 'int',
        'updated_at' => 'datetime',
    ];
}
