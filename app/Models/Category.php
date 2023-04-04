<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    public function images()
    {
        return $this->morphMany(Image::class, 'parentable');
    }

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}