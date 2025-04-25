<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\AssignCase;
use Carbon\Carbon;
use App\Models\Drp;
use App\Models\FileCase;
use App\Models\Notice;
use App\Models\NoticeTemplate;
use App\Models\OrderSheet;
use App\Models\Setting;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Config;
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
                    'file_cases.case_number',
                    'file_cases.loan_number',
                    'file_cases.status',
                    'file_cases.created_at',
                    'assign_cases.sendto_casemanager',
                    'assign_cases.receiveto_casemanager',
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
                ->addColumn('send_status', function ($row) {
                    return $row['sendto_casemanager'] == 0
                        ? '<small class="badge fw-semi-bold rounded-pill badge-danger">Not Sent</small>'
                        : '<small class="badge fw-semi-bold rounded-pill badge-success">Sent</small>';
                })
                ->addColumn('receive_status', function ($row) {
                    return $row['receiveto_casemanager'] == 0
                        ? '<small class="badge fw-semi-bold rounded-pill badge-danger">Not Received</small>'
                        : '<small class="badge fw-semi-bold rounded-pill badge-success">Received</small>';
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
                        $btn .= '<a class="dropdown-item" href="' . route('caseassign.edit', $row['id']) . '">Edit</a>'; // Added Edit Button
                    }
                    if (Helper::userCan(111, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }

                    return Helper::userAllowed(111) ? $btn : '';
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'status', 'assigned_status', 'send_status', 'receive_status'])
                ->make(true);
        }
        return view('caseassign.index');
    }

    public function edit($id)
    {
        $case = FileCase::findOrFail($id);
        return view('caseassign.edit', compact('case'));
    }

    public function updateCaseDetail(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'claimant_name' => 'required|string|max:255',
            'respondent_name' => 'required|string|max:255',
            'case_type' => 'required',
            'status' => 'required',
        ]);

        $case = FileCase::findOrFail($id);

        // Handle file uploads only if new files are uploaded
        $uploadApplicationFormPath = $request->hasFile('application_form') 
            ? Helper::saveFile($request->file('application_form'), 'casefile') 
            : $case->application_form;

        $uploadForeclosureStatementPath = $request->hasFile('foreclosure_statement') 
            ? Helper::saveFile($request->file('foreclosure_statement'), 'casefile') 
            : $case->foreclosure_statement; // Fixed: Should be $case->foreclosure_statement

        $uploadLoanAgreementPath = $request->hasFile('loan_agreement') 
            ? Helper::saveFile($request->file('loan_agreement'), 'casefile') 
            : $case->loan_agreement; // Fixed: Should be $case->loan_agreement

        $uploadAccountStatementPath = $request->hasFile('account_statement') 
            ? Helper::saveFile($request->file('account_statement'), 'casefile') 
            : $case->account_statement; // Fixed: Should be $case->account_statement

        $uploadOtherDocumentPath = $request->hasFile('other_document') 
            ? Helper::saveFile($request->file('other_document'), 'casefile') 
            : $case->other_document; // Fixed: Should be $case->other_document

        $case->update([
            'claimant_first_name' => $request->claimant_name,
            'respondent_first_name' => $request->respondent_name,
            'case_type' => $request->case_type,
            'status' => $request->status,
            'application_form'          => $uploadApplicationFormPath,
            'foreclosure_statement'     => $uploadForeclosureStatementPath,
            'loan_agreement'            => $uploadLoanAgreementPath,
            'account_statement'         => $uploadAccountStatementPath,
            'other_document'            => $uploadOtherDocumentPath,
        ]);

        return redirect()->route('caseassign')->with('success', 'Case details updated successfully.');
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

    

    public function updateassigndetail(Request $request, $id): RedirectResponse
    {
        $caseData = FileCase::with('file_case_details')->find($id);
        
        if (!$caseData) return to_route('caseassign')->withError('Case Not Found..!!');

        $data = $request->validate([
            // 'case_id'          => ['required'],
            'arbitrator_id'    => ['required'],
            // 'advocate_id'      => ['required'],
            'case_manager_id'  => ['required'],
            // 'mediator_id'      => ['required'],
            'conciliator_id'   => ['required'],
        ]);

        AssignCase::updateOrCreate(
            ['case_id' => $id],
            array_merge([
                'case_id'        => $id,
                'arbitrator_id'   => implode(',', $data['arbitrator_id'] ?? []),
                'case_manager_id'=> $data['case_manager_id'],
                'conciliator_id' => $data['conciliator_id'],
                'sendto_casemanager' => 1,
            ])               
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
