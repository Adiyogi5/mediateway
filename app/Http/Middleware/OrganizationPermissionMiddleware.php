<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OrganizationPermissionMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $staff = auth('organization')->user();

        if ($staff) {
            // Make sure it loads OrganizationPermission, not OrganizationRolePermission
            $data = $staff->organization_permission ?? collect();
            $request->merge(['organization_permission' => $data]);
        }
        // if ($staff) {
        //     $data = $staff->load('organization_permission')->organization_permission ?? collect();
        //     $request->merge(['organization_permission' => $data]);
        // }

        return $next($request);
    }
}
