<?php

namespace App\Models;

use App\Traits\CustomScopes;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrganizationRolePermission extends Model
{
    use HasFactory, CustomScopes;

    public $timestamps = false;

    protected $fillable = [
        'organization_role_id',
        'module_id',
        'can_view',
        'can_add',
        'can_edit',
        'can_delete',
        'allow_all',
    ];

    public function toggle($type = '')
    {
        $this->update([$type => DB::raw('NOT ' . $type)]);
    }

    public function permission_name()
    {
        return $this->belongsTo(OrganizationPermissionModule::class, 'module_id');
    }
}
