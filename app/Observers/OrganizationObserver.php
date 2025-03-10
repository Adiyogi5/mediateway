<?php

namespace App\Observers;

use App\Models\Organization;
use App\Helper\Helper;
use Illuminate\Support\Str;

class OrganizationObserver
{
    public function creating(Organization $organization)
    {
        $organization->slug      = Str::uuid();
        // $organization->image     = $organization->image ?? 'admin/avatar.png';
    }

    public function saving(Organization $organization)
    {
        $organization->slug      = Str::uuid();
        // $organization->image     = $organization->image ?? 'admin/avatar.png';
    }

    public function created(Organization $organization)
    {
        // Code after save
        $organization->organizationId = Helper::orderId($organization->id, 'ORG', 6);
        $organization->saveQuietly();
    }
}
