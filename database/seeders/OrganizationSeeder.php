<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Organization::create([
            'id'                            => 1,
            'slug'                          => Str::uuid(),
            'name'                          => 'Ravindra',
            'email'                         => 'ravindra@gmail.com',
            'mobile'                        => '8741066111',
            'status'                        => '1',
            'organization_role_id'          => 1,
            'image'                         => 'admin/avatar.png',
            'email_verified_at'             => NULL,
            'remember_token'                => 'CfaY4OZWO7bLxsnytPwn78B2mxdnGJcW16JNgYawHvCa6x85UMRkNLOyBxn1',
            'email_verified_at'             => Carbon::now(),
            'created_at'                    => Carbon::now(),
            'updated_at'                    => Carbon::now(),
        ]);
    }
}
