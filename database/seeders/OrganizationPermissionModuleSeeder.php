<?php

namespace Database\Seeders;

use App\Models\OrganizationPermissionModule;
use Illuminate\Database\Seeder;

class OrganizationPermissionModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        //  Permission Array 
        $organization_permissions = [
            [
                'module_id'     => '201',
                'name'          => 'Dashboard',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '202',
                'name'          => 'Roles',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '203',
                'name'          => 'Staff',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '204',
                'name'          => 'Profile',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '205',
                'name'          => '...',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '206',
                'name'          => '...',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '207',
                'name'          => '...',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '208',
                'name'          => '...',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '209',
                'name'          => '...',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '210',
                'name'          => '...',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '211',
                'name'          => '...',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '212',
                'name'          => '...',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '213',
                'name'          => '...',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '214',
                'name'          => '...',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
            [
                'module_id'     => '215',
                'name'          => '...',
                'can_add'       => 0,
                'can_edit'      => 0,
                'can_delete'    => 0,
                'can_view'      => 0,
                'allow_all'     => 0
            ],
        ];

        OrganizationPermissionModule::insert($organization_permissions);
    }
}
