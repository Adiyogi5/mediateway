<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\AssignCase;
use Carbon\Carbon;
use App\Models\Drp;
use App\Models\FileCase;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ConciliationCaseAssignController extends Controller
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
                    'file_cases.product_type',
                    'file_cases.case_number',
                    'file_cases.loan_number',
                    'file_cases.status',
                    'file_cases.created_at',
                    'assign_cases.is_assigned',
                )
                ->leftJoin('assign_cases', 'assign_cases.case_id', '=', 'file_cases.id')
                ->where('file_cases.case_type', 3)
                ->where('assign_cases.is_assigned', 1);

            // Apply Filters
            if ($request->filled('user_type')) {
                $data->where('file_cases.user_type', $request->user_type);
            }
            if ($request->filled('status')) {
                $data->where('file_cases.status', $request->status);
            }
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $data->whereBetween('file_cases.created_at', [
                    $request->date_from . ' 00:00:00',
                    $request->date_to . ' 23:59:59'
                ]);
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

        $casemanagers = Drp::where('drp_type', 3)->where('approve_status', 1)->where('status', 1)->get();
        $conciliators = Drp::where('drp_type', 5)->where('approve_status', 1)->where('status', 1)->get();

        return view('conciliation_caseassign.index', compact('casemanagers', 'conciliators'));
    }


    public function unassignedCases(Request $request)
    {
        $data = FileCase::select(
                'file_cases.id',
                'file_cases.case_number',
                'file_cases.loan_number',
                'file_cases.user_type',
                'file_cases.status',
                'file_cases.created_at'
            )
            ->leftJoin('assign_cases', 'assign_cases.case_id', '=', 'file_cases.id')
            ->where('file_cases.case_type', 3)
            ->where(function ($q) {
                $q->whereNull('assign_cases.is_assigned')
                ->orWhere('assign_cases.is_assigned', 0);
            });

        return DataTables::of($data)
            ->editColumn('user_type', fn($row) => $row->user_type == 1 ? 'Individual' : 'Organization')
            ->editColumn('status', fn($row) =>
                $row->status == 1
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>'
            )
            ->editColumn('created_at', fn($row) => \Carbon\Carbon::parse($row->created_at)->format('d M, Y'))
            ->rawColumns(['status'])
            ->make(true);
    }


    public function bulkAssign(Request $request)
    {
        $request->validate([
            'case_manager_id' => 'required|exists:drps,id',
            'conciliator_id' => 'required|exists:drps,id',
        ]);

        $unassignedCases = FileCase::select('file_cases.id')
            ->leftJoin('assign_cases', 'assign_cases.case_id', '=', 'file_cases.id')
            ->where('file_cases.case_type', 3)
            ->where(function ($q) {
                $q->whereNull('assign_cases.is_assigned')
                ->orWhere('assign_cases.is_assigned', 0);
            })
            ->get();

        // If none found
        if ($unassignedCases->isEmpty()) {
            return redirect()->back()->with('warning', 'No unassigned cases available to assign.');
        }

        // Assign all
        foreach ($unassignedCases as $case) {
            AssignCase::updateOrCreate(
                ['case_id' => $case->id],
                [
                    'case_manager_id' => $request->case_manager_id,
                    'conciliator_id' => $request->conciliator_id,
                    'is_assigned' => 1,
                ]
            );
        }

        return redirect()->route('conciliation_caseassign')->with('success', 'All unassigned cases assigned successfully.');
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
