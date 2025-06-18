<?php

namespace App\Http\Controllers\Drp;

use App\Http\Controllers\Controller;
use App\Helper\Helper;
use App\Models\AssignCase;
use App\Models\Country;
use App\Models\Drp;
use App\Models\FileCase;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use App\Exports\FileCaseExport;
use Maatwebsite\Excel\Facades\Excel;

class CaseAssignController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:drp');
    }

    public function index(Request $request): View|JsonResponse
    {
        $drp = auth('drp')->user();
        // Ensure the user is authenticated and has drp_type == 1
        if (!auth('drp')->check() || auth('drp')->user()->drp_type != 3) {
            return redirect()->route('drp.dashboard')->with('error', 'UnAuthentication Access..!!');
        }
        if ($drp->approve_status !== 1) {
            return redirect()->route('drp.dashboard')->withError('DRP is Not Approved by Mediateway.');
        }

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
                    'assign_cases.confirm_to_arbitrator',
                    DB::raw("IF(assign_cases.id IS NULL, 0, 1) as is_assigned"),
                    DB::raw("IF(
                            assign_cases.arbitrator_id IS NULL OR 
                            assign_cases.advocate_id IS NULL OR 
                            assign_cases.mediator_id IS NULL OR 
                            assign_cases.conciliator_id IS NULL, 0, 1) as is_fully_assigned")
                )
                ->leftJoin('assign_cases', 'assign_cases.case_id', '=', 'file_cases.id')
                ->where('assign_cases.case_manager_id', '=', auth('drp')->user()->id);

            // Apply Filters
            if ($request->filled('user_type')) {
                $data->where('file_cases.user_type', $request->user_type);
            }
            if ($request->filled('case_type')) {
                $data->where('file_cases.case_type', $request->case_type);
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
                ->addColumn('send_status', function ($row) {
                    return $row['sendto_casemanager'] == 0
                    ? '<small class="badge fw-semi-bold rounded-pill badge-danger">Not Received</small>'
                    : '<small class="badge fw-semi-bold rounded-pill badge-success">Received</small>';
                       
                })
                ->addColumn('receive_status', function ($row) {
                    return $row['receiveto_casemanager'] == 0
                    ? '<small class="badge fw-semi-bold rounded-pill badge-danger">Not Sent</small>'
                    : '<small class="badge fw-semi-bold rounded-pill badge-success">Sent</small>';
                })    
                ->addColumn('arbitrator_status', function ($row) {
                    return $row['confirm_to_arbitrator'] == 0
                        ? '<small class="badge fw-semi-bold rounded-pill badge-danger">Pending</small>'
                        : '<small class="badge fw-semi-bold rounded-pill badge-success">Confirmed</small>';
                }) 
                ->addColumn('assigned_status', function ($row) {
                    return $row['is_fully_assigned'] ? 
                        '<small class="badge fw-semi-bold rounded-pill badge-success">Assigned</small>' : 
                        '<small class="badge fw-semi-bold rounded-pill badge-danger">Not Assigned</small>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                        $btn .= '<a class="dropdown-item" href="' . route('drp.caseassign.assign', $row['id']) . '">Assign</a>';
                        $btn .= '<a class="dropdown-item" href="' . route('drp.caseassign.edit', $row['id']) . '">Edit</a>'; // Added Edit Button
                   
                    return $btn ;
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'status', 'assigned_status', 'send_status', 'receive_status', 'arbitrator_status'])
                ->make(true);
        }
        $title = 'Cases Assign';
        return view('drp.caseassign.index', compact('title'));
    }

    public function edit($id)
    {
        $drp = auth('drp')->user();
        // Ensure the user is authenticated and has drp_type == 1
        if (!auth('drp')->check() || auth('drp')->user()->drp_type != 3) {
            return redirect()->route('drp.dashboard')->with('error', 'UnAuthentication Access..!!');
        }
        if ($drp->approve_status !== 1) {
            return redirect()->route('drp.dashboard')->withError('DRP is Not Approved by Mediateway.');
        }

        $case = FileCase::findOrFail($id);
        $title = 'Cases Assign Edit';

        return view('drp.caseassign.edit', compact('case','title'));
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

        return redirect()->route('drp.caseassign')->with('success', 'Case details updated successfully.');
    }


    public function assign($id): View|RedirectResponse
    {
        $drp = auth('drp')->user();
        // Ensure the user is authenticated and has drp_type == 1
        if (!auth('drp')->check() || auth('drp')->user()->drp_type != 3) {
            return redirect()->route('drp.dashboard')->with('error', 'UnAuthentication Access..!!');
        }
        if ($drp->approve_status !== 1) {
            return redirect()->route('drp.dashboard')->withError('DRP is Not Approved by Mediateway.');
        }

        $caseData = FileCase::with(['individual', 'organization'])->find($id);
       
        $assignCase = AssignCase::where('case_id', $id)->first();

        // Convert comma-separated arbitrator_ids to array
        $arbitratorIds = $assignCase ? explode(',', $assignCase->arbitrator_id) : [];

        // Fetch only saved arbitrators (by their IDs)
        $arbitrators = Drp::where('drp_type', 1)->whereIn('id', $arbitratorIds)->get();
        $advocates = Drp::where('drp_type', 2)->get();
        $casemanagers = Drp::where('drp_type', 3)->get();
        $mediators = Drp::where('drp_type', 4)->get();
        $conciliators = Drp::where('drp_type', 5)->get();

        if (!$caseData) return to_route('drp.caseassign')->withError('Case Not Found..!!');

        $title = 'Case Assign';

        return view('drp.caseassign.assign', compact('title', 'caseData','assignCase','arbitrators','advocates','casemanagers','mediators','conciliators'));
    }

    public function updateassigndetail(Request $request, $id): RedirectResponse
    {
        $caseData = FileCase::find($id);
        if (!$caseData) return to_route('drp.caseassign')->withError('Case Not Found..!!');

        $data = $request->validate([
            // 'case_id'          => ['required'],
            'arbitrator_id'    => ['required'],
            'advocate_id'      => ['required'],
            // 'case_manager_id'  => ['required'],
            'mediator_id'      => ['required'],
            'conciliator_id'   => ['required'],
        ]);

        AssignCase::updateOrCreate(    
            ['case_id' => $id],
            array_merge([
                'case_id'           => $id,
                'arbitrator_id'     => implode(',', $data['arbitrator_id'] ?? []),
                'advocate_id'       => $data['advocate_id'],
                'mediator_id'       => $data['mediator_id'],
                'conciliator_id'    => $data['conciliator_id'],
                'receiveto_casemanager' => 1,
            ])            
        );

        // Send SMS Invitation using Twilio
        // try {
        //     $sid    = env("TWILIO_ACCOUNT_SID");
        //     $token  = env("TWILIO_AUTH_TOKEN");
        //     $sender = env("TWILIO_SENDER");

        //     $client = new Client($sid, $token);

        //     $country_data = Country::where('id', $request->country_id)->where('status', 1)->first();
        //     $phone_code = $country_data->phone_code ?? '';

        //     $message = "{$user->name} has invited you to join Patrimonial, an online testament and wealth management App, to securely manage and access patrimonial information. Accept the invitation here: https://www.name/login";

        //     $client->messages->create($phone_code . $request->mobile, [
        //         'from' => $sender,
        //         'body' => $message,
        //     ]);
        // } catch (\Throwable $th) {
        //     // Log SMS error but don't stop execution
        //     Log::error('SMS sending failed: ' . $th->getMessage());
        // }

        return to_route('drp.caseassign')->withSuccess('Case Assign Successfully..!!');
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
