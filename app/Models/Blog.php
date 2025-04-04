<?php

namespace App\Models;

use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $table = 'blogs';

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
