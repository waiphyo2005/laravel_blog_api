<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogMediaContents extends Model
{
    /** @use HasFactory<\Database\Factories\BlogMediaContentsFactory> */
    use HasFactory;

    protected $fillable = [
        'media_name',
        'media_path_url',
        'is_used',
        'user_id'
    ];
}
