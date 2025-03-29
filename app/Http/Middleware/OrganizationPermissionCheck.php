<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OrganizationPermissionCheck
{
    public function handle(Request $request, Closure $next, int $organization_module = 0, string $type = '')
    {
        if (!auth('organization')->check()) {
            return $next($request);
        }

        $organization_permission = $request->organization_permission;

        if (!$organization_permission || $organization_permission->isEmpty()) {
            return self::errorNotFound($request);
        }

        $module_permission = $organization_permission->firstWhere('module_id', $organization_module);

        if (!$module_permission) {
            return self::errorNotFound($request);
        }

        if ($module_permission->allow_all == 1 || ($type && ($module_permission[$type] ?? 0) == 1)) {
            return $next($request);
        }

        return self::errorNotFound($request);
    }

    protected static function errorNotFound(Request $request)
    {
        return $request->ajax()
            ? response()->json(['status' => false, 'message' => 'Route not found'], 404)
            : abort(404);
    }
}
