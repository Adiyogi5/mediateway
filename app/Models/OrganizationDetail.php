<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrganizationDetail extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $fillable = [
        'organization_id', 'organization_type', 'description', 'registration_no', 'registration_certificate', 'attach_registration_certificate',
    ];
}
