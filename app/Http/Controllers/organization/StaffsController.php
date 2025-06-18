<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Helper\Helper;
use App\Http\Requests\OrganizationRequest;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use App\Models\Organization;
use App\Models\OrganizationPermission;
use App\Models\OrganizationPermissionModule;
use App\Models\OrganizationRole;
use App\Models\OrganizationRolePermission;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;

class StaffsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:organization');
    }

    public function index(Request $request): View|JsonResponse
    {
        $organization = auth('organization')->user();

        if ($request->ajax()) {
            $data = Organization::select('organizations.id', 'organizations.name', 'organizations.email', 'organizations.slug', 'organizations.mobile', 'organizations.image', 'organizations.status', 'organizations.created_at', 'organization_roles.name as organization_role_name')
                ->where('organizations.organization_role_id', '!=', 1)
                ->where('organizations.parent_id', auth()->id())
                ->leftJoin('organization_roles', 'organization_roles.id', '=', 'organizations.organization_role_id');
            return Datatables::of($data)
                ->editColumn('image', function ($row) {
                    $btn = '<div class="avatar avatar-md"><img class="rounded-circle" src="' . asset('storage/' . $row['image']) . '" alt=""></div>';
                    return $btn;
                })
                ->editColumn('name', function ($row) {
                    return '<b class="text-dark">' . $row['name'] . '</b><br /> <span class="text-secondary">(' . $row['organization_role_name'] . ')<span>';
                })
                ->editColumn('email', function ($row) {
                    return '<b class="text-dark">' . $row['email'] . '</b><br /> <b class="text-dark">' . $row['mobile'] . '<span>';
                })
                ->editColumn('created_at', function ($row) {
                    return $row['created_at']->format('d M, Y');
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (Helper::organizationCan(203, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('organization.staffs.edit', $row->slug) . '">Edit</a>';
                        $btn .= '<a class="dropdown-item" href="' . route('organization.staffs.permission.view', $row->slug) . '">Permission</a>';
                    }
                    if (Helper::organizationCan(203, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }

                    if (Helper::organizationAllowed(203)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'email', 'name', 'image', 'status'])
                ->make(true);
        }

        $title = 'Staff';

        ################## Profile Incomplete Start ##################
        $organizationdata = Organization::with('organizationDetail')->where('id', $organization->id)->first();

        // Required fields to check
        $requiredFields = [
            'name', 'email', 'mobile', 'state_id', 'city_id', 'pincode', 'image', 
            'address1', 
        ];
        $requiredDetailFields = ['registration_no', 'registration_certificate', 'attach_registration_certificate'];

        // Check if any field is null or empty
        $missingFields = collect($requiredFields)->filter(fn($field) => empty($organizationdata->$field));
        $missingDetailFields = collect($requiredDetailFields)->filter(fn($field) => empty($organizationdata->organizationDetail?->$field));

        if ($missingFields->isNotEmpty() || $missingDetailFields->isNotEmpty()) {
            return view('organization.staffs.index', compact(
                'organizationdata',
                'title',
                ))
                ->with('showProfilePopup', true);
        }
        ################## Profile Incomplete End ##################

        return view('organization.staffs.index',compact('title'));
    }

    public function add(): View
    {
        $title = 'Add Staff';
        $organization_authData = auth('organization')->user();

        $roles = OrganizationRole::active()->whereNot('id', 1)->get();
        return view('organization.staffs.add', compact('roles','title','organization_authData'));
    }

    public function save(OrganizationRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $organization = Organization::create($request->filter());
            $data = OrganizationRolePermission::where('organization_role_id', $request->role_id)->get()->map(function ($value) use ($organization) {
                return [
                    'organization_id'       => $organization->id,
                    'module_id'     => $value->module_id,
                    'can_view'      => $value->can_view,
                    'can_add'       => $value->can_add,
                    'can_edit'      => $value->can_edit,
                    'can_delete'    => $value->can_delete,
                    'allow_all'     => $value->allow_all,
                ];
            });

            OrganizationPermission::insert($data->toArray());
        });

        return to_route('organization.staffs')->withSuccess('Organization Staff Added Successfully..!!');
    }

    public function edit($slug): View|RedirectResponse
    {
        $title = 'Edit Staff';
        $organization_authData = auth('organization')->user();
        $roles  = OrganizationRole::active()->whereNot('id', 1)->get();
        $organization   = Organization::slug($slug);
        
        if (!$organization) {
            return to_route('organization.staffs')->withError('Organization Staff Not Found..!!');
        }
        return view('organization.staffs.edit', compact('organization', 'roles','title','organization_authData'));
    }
    
    public function update(OrganizationRequest $request, $slug): RedirectResponse
    {
        // dd($request->all());
        $organization   = Organization::slug($slug);
        if (!$organization) {
            return to_route('organization.staffs')->withError('Organization Staff Not Found..!!');
        }

        $organization->update($request->filter($organization));
        return to_route('organization.staffs')->withSuccess('Organization Staff Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new Organization, $request->id);
    }

    public function permission($slug): View|RedirectResponse
    {
        $title = 'Staff Permissions';
        $organization = Organization::slug($slug);
        if (!$organization) {
            return to_route('organization.staffs')->withError('Organization Staff Not Found..!!');
        }

        $permissions = OrganizationPermissionModule::select('organization_permissions.*', 'organization_permission_modules.id as modules_id', 'organization_permission_modules.module_id as module_id', 'organization_permission_modules.name')
            ->leftJoin('organization_permissions', function ($join) use ($organization) {
                $join->on('organization_permissions.module_id', '=', 'organization_permission_modules.module_id')
                    ->where('organization_permissions.organization_id', $organization->id);
            })->get();

        if (!$organization) {
            return to_route('organization.staffs')->withError('Organization Staff Not Found..!!');
        }

        return view('organization.staffs.permission', compact('organization', 'permissions','title'));
    }

    public function permission_update(Request $request): bool
    {
        $organization_permission = OrganizationPermission::firstWhere(['organization_id' => $request->organization_id, 'module_id' => $request->module_id]);
        if (!$organization_permission) {
            OrganizationPermission::create([
                'organization_id'       => $request->organization_id,
                'module_id'     => $request->module_id,
                'can_view'      => $request->type == 'can_view' ? 1 : 0,
                'can_add'       => $request->type == 'can_add' ? 1 : 0,
                'can_edit'      => $request->type == 'can_edit' ? 1 : 0,
                'can_delete'    => $request->type == 'can_delete' ? 1 : 0,
                'allow_all'     => $request->type == 'allow_all' ? 1 : 0,
            ]);
            return true;
        }

        if (array($request->type, ['can_view', 'can_add', 'can_edit', 'can_delete', 'allow_all'])) {
            $organization_permission->toggle($request->type);
            return  true;
        }
        return false;
    }
}
