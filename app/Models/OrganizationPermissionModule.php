<?php

namespace App\Models;

use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationPermissionModule extends Model
{
    use HasFactory, CustomScopes;

    public $timestamps = false;
}
