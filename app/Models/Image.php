<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasUuids;

    protected $fillable = [
        'filename',
        'full_path',
        'mime_type',
        'size',
        'content',
        'created_by_id',
    ];

    public function imageable()
    {
        return $this->morphTo();
    }

    public function created_by()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
