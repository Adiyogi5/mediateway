<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Helper\Helper;
use App\Models\Organization;
use App\Models\OrganizationPermission;
use App\Models\OrganizationPermissionModule;
use App\Models\OrganizationRole;
use App\Models\OrganizationRolePermission;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\RolePermission;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;

class StaffRolesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:organization');
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = OrganizationRole::select('id', 'name', 'slug', 'status')->whereNot('id', 1);
            return Datatables::of($data)
                ->addColumn('action', function ($row) {
                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (Helper::organizationCan(202, 'can_edit')) {
                        $btn .= '<button class="dropdown-item edit" data-all="' . htmlspecialchars(json_encode($row))  . '">Edit</button>';
                        $btn .= '<a class="dropdown-item" href="' . route('organization.staffroles.permission.view', $row->slug) . '">Permission</a>';
                    }
                    if (Helper::organizationCan(202, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }

                    if (Helper::organizationAllowed(202)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        $title = 'Staff Roles';
        return view('organization.staffroles.index',compact('title'));
    }

    public function save(Request $request): JsonResponse
    {
        return Helper::checkValid([
            'name'      => ['required', 'string', 'max:100', 'unique:organization_roles,name,NULL,id,deleted_at,NULL'],
            'status'    => ['required', 'integer'],
        ], function ($validator) {

            DB::transaction(function () use ($validator) {
                $new_role   = OrganizationRole::create([...$validator->validated(), 'slug'  => Str::uuid()]);
                $data       = OrganizationPermissionModule::select('module_id')->get()->map(function ($value) use ($new_role) {
                    return [
                        'organization_role_id'       => $new_role->id,
                        'module_id'     => $value->module_id,
                        'can_view'      => 0,
                        'can_add'       => 0,
                        'can_edit'      => 0,
                        'can_delete'    => 0,
                        'allow_all'     => 0,
                    ];
                });

                OrganizationRolePermission::insert($data->toArray());
            });

            return response()->json([
                'status'    => true,
                'message'   => 'Staff Role Added Successfully',
                'data'      => ''
            ]);
        });
    }

    public function update(Request $request): JsonResponse
    {
        $role = OrganizationRole::find($request->id);
        if (!$role) {
            return response()->json([
                'status'    => false,
                'message'   => 'Staff Role Not Found..!!',
            ]);
        }

        return Helper::checkValid([
            'id'        => ['required'],
            'name'      => ['required', 'string', 'max:100', 'unique:organization_roles,name,' . $role['id'] . ',id,deleted_at,NULL'],
            'status'    => ['required', 'integer'],
        ], function ($validator) use ($role) {
            $role->update($validator->validated());
            return response()->json([
                'status'    => true,
                'message'   => 'Staff Role Updated Successfully',
            ]);
        });
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new OrganizationRole, $request->id);
    }

    public function permission($slug = null): View|RedirectResponse
    {
        $title = 'Staff Roles Permission';
        $role = OrganizationRole::slug($slug);
        if (!$role) {
            return to_route('organization.staffroles')->withError('Staff Role Not Found..!!');
        }

        $permissions = OrganizationPermissionModule::select('organization_role_permissions.*', 'organization_permission_modules.module_id as module_id', 'organization_permission_modules.id as modules_id', 'organization_permission_modules.name')
            ->leftJoin('organization_role_permissions', function ($join) use ($role) {
                $join->on('organization_role_permissions.module_id', '=', 'organization_permission_modules.module_id')
                    ->where('organization_role_permissions.organization_role_id', $role['id']);
            })->get();

        if (!$role) {
            return to_route('organization.staffroles')->withError('Staff Role Not Found..!!');
        }
        return view('organization.staffroles.permission', compact('role', 'permissions','title'));
    }

    public function permission_update(Request $request): bool
    {
        $role_permission = OrganizationRolePermission::firstWhere(['organization_role_id' => $request->role_id, 'module_id' => $request->module_id]);
        if (!$role_permission) {
            OrganizationRolePermission::create([
                'organization_role_id'       =>  $request->role_id,
                'module_id'     => $request->module_id,
                'can_view'      => $request->type == 'can_view' ? 1 : 0,
                'can_add'       => $request->type == 'can_add' ? 1 : 0,
                'can_edit'      => $request->type == 'can_edit' ? 1 : 0,
                'can_delete'    => $request->type == 'can_delete' ? 1 : 0,
                'allow_all'     => $request->type == 'allow_all' ? 1 : 0,
            ]);
            return true;
        }

        $val = $role_permission[$request->type] == 1 ? 0 : 1;
        if (array($request->type, ['can_view', 'can_add', 'can_edit', 'can_delete',  'allow_all'])) {
            $role_permission->toggle($request->type);
            $users = Organization::where('organization_role_id', $role_permission->role_id)->get()->pluck('id');
            OrganizationPermission::whereIn('organization_id', $users)->where('module_id', $role_permission->module_id)->update([$request->type => $val]);
            return  true;
        }
        return false;
    }
}
