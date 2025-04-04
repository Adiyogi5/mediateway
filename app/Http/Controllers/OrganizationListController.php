<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\OrganizationList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use \Yajra\Datatables\Datatables;

class OrganizationListController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = OrganizationList::query();

            return Datatables::of($data)
                ->addColumn('action', function ($row) {
                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (Helper::userCan(112, 'can_edit')) {
                        $btn .= '<button class="dropdown-item edit" data-all="' . htmlspecialchars(json_encode($row)) . '">Edit</button>';
                    }
                    if (Helper::userCan(112, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }
                    $btn .= '</div>'; // Make sure to close dropdown
                    return Helper::userAllowed(112) ? $btn : '';
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1
                        ? '<small class="badge fw-semi-bold rounded-pill badge-light-success">Active</small>'
                        : '<small class="badge fw-semi-bold rounded-pill badge-light-danger">Inactive</small>';
                })
                ->editColumn('created_at', fn ($row) => $row->created_at->format('d F, Y'))
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('organizationlist.index');
    }

    public function save(Request $request): JsonResponse
    {
        return Helper::checkValid([
            'name'   => ['required'],
            'code'   => ['required'],
            'status' => ['required', 'integer'],
        ], function ($validator) {
            $data = $validator->validated();
            OrganizationList::create($data);

            return response()->json([
                'status'  => true,
                'message' => 'Organization added successfully.',
                'data'    => '',
            ]);
        });
    }

    public function update(Request $request): JsonResponse
    {
        $organizationlist = OrganizationList::find($request->id);
        if (!$organizationlist) {
            return response()->json([
                'status'  => false,
                'message' => 'Organization not found!',
                'data'    => '',
            ]);
        }

        return Helper::checkValid([
            'name'   => ['required'],
            'code'   => ['required'],
            'status' => ['required', 'integer'],
        ], function ($validator) use ($organizationlist) {
            $data = $validator->validated();
            $organizationlist->update($data);

            return response()->json([
                'status'  => true,
                'message' => 'Organization updated successfully.',
                'data'    => '',
            ]);
        });
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new OrganizationList, $request->id);
    }
}
