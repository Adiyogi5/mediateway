<?php

namespace Database\Seeders;

use App\Models\OrganizationPermissionModule;
use App\Models\OrganizationRolePermission;
use Illuminate\Database\Seeder;

class OrganizationRolePermissionSeeder extends Seeder
{

    public function run()
    {
        $organization_role_id = 1;
        $all_permissions = OrganizationPermissionModule::all();
        $data = [];
        foreach ($all_permissions as $key => $value) {
            array_push($data, [
                'organization_role_id'       => $organization_role_id,
                'module_id'     => $value->module_id,
                'can_view'      => 1,
                'can_add'       => 1,
                'can_edit'      => 1,
                'can_delete'    => 1,
                'allow_all'     => 1,
            ]);
        }
        OrganizationRolePermission::insert($data);
    }
}
