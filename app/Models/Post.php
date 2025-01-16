<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasUuids;

    protected $fillable = [
        'slug',
        'title',
        'content',
        'created_by_id',
    ];

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'tagable');
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
