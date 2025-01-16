<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasUlids;

    protected $fillable = [
        'name',
    ];

    public function posts()
    {
        return $this->morphedByMany(Post::class, 'tagable');
    }

    public function videos()
    {
        return $this->morphedByMany(Product::class, 'tagable');
    }
}
