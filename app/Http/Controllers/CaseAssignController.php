<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\AssignCase;
use App\Models\Drp;
use App\Models\FileCase;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class CaseAssignController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = FileCase::select(
                    'file_cases.id',
                    'file_cases.user_type',
                    'file_cases.case_type',
                    'file_cases.claimant_first_name',
                    'file_cases.respondent_first_name',
                    'file_cases.status',
                    'file_cases.created_at',
                    DB::raw("IF(assign_cases.id IS NULL, 0, 1) as is_assigned") 
                )
                ->leftJoin('assign_cases', 'assign_cases.case_id', '=', 'file_cases.id');

            // Apply Filters
            if ($request->filled('user_type')) {
                $data->where('file_cases.user_type', $request->user_type);
            }
            if ($request->filled('case_type')) {
                $data->where('file_cases.case_type', $request->case_type);
            }
            if ($request->filled('created_at')) {
                $data->whereDate('file_cases.created_at', $request->created_at);
            }

            return Datatables::of($data)
                ->editColumn('user_type', function ($row) {
                    return $row['user_type'] == 1 ? 'Individual' : 'Organization';
                })
                ->editColumn('case_type', function ($row) {
                    $caseTypes = config('constant.case_type');
                    return $caseTypes[$row['case_type']] ?? 'Unknown';
                })
                ->editColumn('created_at', function ($row) {
                    return $row['created_at']->format('d M, Y');
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? 
                        '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : 
                        '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->addColumn('assigned_status', function ($row) {
                    return $row['is_assigned'] ? 
                        '<small class="badge fw-semi-bold rounded-pill badge-success">Assigned</small>' : 
                        '<small class="badge fw-semi-bold rounded-pill badge-danger">Not Assigned</small>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';

                    if (Helper::userCan(111, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('caseassign.assign', $row['id']) . '">Assign</a>';
                    }
                    if (Helper::userCan(111, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }

                    return Helper::userAllowed(111) ? $btn : '';
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'status', 'assigned_status'])
                ->make(true);
        }
        return view('caseassign.index');
    }

    public function assign($id): View|RedirectResponse
    {
        $caseData = FileCase::with(['individual', 'organization'])->find($id);
       
        $assignCase = AssignCase::where('case_id', $id)->first();
        
        $arbitrators = Drp::where('drp_type', 1)->get();
        $advocates = Drp::where('drp_type', 2)->get();
        $casemanagers = Drp::where('drp_type', 3)->get();
        $mediators = Drp::where('drp_type', 4)->get();
        $conciliators = Drp::where('drp_type', 5)->get();

        if (!$caseData) return to_route('caseassign')->withError('Case Not Found..!!');

        return view('caseassign.assign', compact('caseData','assignCase','arbitrators','advocates','casemanagers','mediators','conciliators'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $caseData = FileCase::find($id);
        if (!$caseData) return to_route('caseassign')->withError('Case Not Found..!!');

        $data = $request->validate([
            // 'case_id'          => ['required'],
            'arbitrator_id'    => ['required'],
            'advocate_id'      => ['required'],
            'case_manager_id'  => ['required'],
            'mediator_id'      => ['required'],
            'conciliator_id'   => ['required'],
        ]);

        AssignCase::updateOrCreate(
            ['case_id' => $id],
            array_merge($data, ['case_id' => $id])               
        );

        return to_route('caseassign')->withSuccess('Case Assign Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        $case = FileCase::find($request->id);
        
        if (!$case) {
            return response()->json([
                'status'  => false,
                'message' => 'No Record Found..!!',
            ]);
        }
    
        // Delete related AssignCase records
        AssignCase::where('case_id', $case->id)->delete();
    
        // Delete the FileCase record
        $case->delete();
    
        return response()->json([
            'status'  => true,
            'message' => 'Record Deleted Successfully.!!',
        ]);
    }
    
}
