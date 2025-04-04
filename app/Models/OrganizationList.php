<?php

namespace App\Models;

use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationList extends Model
{
    use HasFactory, CustomScopes;

    protected $fillable = [
        'name',
        'code',
        'status',
    ];

}
