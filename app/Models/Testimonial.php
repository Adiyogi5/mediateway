<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonial extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $fillable = [
        'name',
        'rating',
        'description',
        'designation',
        'image',
        'status',
    ];
}
