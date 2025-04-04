<?php

namespace App\Models;

use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $table = 'news';
    
    protected $fillable = [
        'title',
        'post_by',
        'date',
        'short_description',
        'description',
        'status',
        'image'
    ];
}
