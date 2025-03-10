<?php

namespace App\Models;

use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrganizationRole extends Model
{
    use  HasFactory, SoftDeletes, CustomScopes;
    protected $fillable = [
        'slug',
        'name',
        'status',
    ];

    public function permission()
    {
        return $this->hasMany(OrganizationRolePermission::class)->with('permission_name');
    }
}
