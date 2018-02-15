<?php

namespace Lampager\Laravel\Tests;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Tag
 *
 * @method static Tag create(array $attributes = [])
 * @method static Tag find(int $id)
 */
class Tag extends Model
{
    protected $fillable = ['id'];

    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
    ];

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}
