<?php

namespace App\Http\Controllers\Organization;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\FileCaseImport;
use App\Exports\SampleFileCaseExport;
use App\Models\FileCase;
use App\Models\Notice;
use App\Models\Organization;
use App\Models\State;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class FileCaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:organization');
    }

    public function index(Request $request): View|JsonResponse
    {
        $title = 'File Cases List';

        $organization = auth('organization')->user();

        if (!$organization) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }

        if ($request->ajax()) {
            $data = FileCase::select('file_cases.id', 'file_cases.case_type', 'file_cases.organization_id', 'file_cases.claimant_first_name', 'file_cases.claimant_last_name', 'file_cases.claimant_mobile', 'file_cases.respondent_first_name', 'file_cases.respondent_last_name', 'file_cases.respondent_mobile', 'file_cases.status', 'file_cases.created_at')
                ->join('organizations', 'file_cases.organization_id', '=', 'organizations.id')
                ->where(function ($query) use ($organization) {
                    if ($organization->parent_id == null) {
                        // If parent organization, show its own cases and cases filed by its child organizations
                        $query->where('file_cases.organization_id', $organization->id)
                              ->orWhere('organizations.parent_id', $organization->id);
                    } else {
                        // If child organization, show only its own filed cases
                        $query->where('file_cases.organization_id', $organization->id);
                    }
                })
                ->where('file_cases.status', 1);
            return Datatables::of($data)
                ->editColumn('case_type', function ($row) {
                    return config('constant.case_type')[$row->case_type] ?? 'Unknown';
                })
                ->editColumn('created_at', function ($row) {
                    return $row['created_at']->format('d M, Y');
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (Helper::organizationCan(205, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('organization.cases.filecaseview.edit', $row->id) . '">Upload Documents</a>';
                    }
                    // if (Helper::organizationCan(205, 'can_delete')) {
                        // $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    // }
                    $btn .= '<a class="dropdown-item" href="' . route('organization.cases.viewcasedetail', $row->id) . '">View Case Details</a>';
                    if (Helper::organizationAllowed(205)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'status','case_type'])
                ->make(true);
        }

        return view('organization.cases.filecaseview', compact('organization','title'));
    }

    public function edit($id): View|RedirectResponse
    {
        $title = 'Edit Filed Case';
        $organization_authData = auth('organization')->user();
        
        // Fetch both notices by type
        $noticeType1 = Notice::where('file_case_id', $id)->where('notice_type', 1)->first();
        $noticeType2 = Notice::where('file_case_id', $id)->where('notice_type', 2)->first();

        $caseviewData   = FileCase::Find($id);
        $states = State::all();
        
        if (!$caseviewData) {
            return to_route('organization.cases.filecaseview')->withError('Filed Case Not Found..!!');
        }
        return view('organization.cases.edit', compact('caseviewData', 'noticeType1', 'noticeType2','title','organization_authData','states'));
    }

    //For Upload Documents By Organization
    public function update(Request $request, $id): RedirectResponse
    {
        // dd($request->all());
        $caseviewData   = FileCase::Find($id);
        $organization_authData = auth('organization')->user();

        if (!$caseviewData) {
            return to_route('organization.cases.filecaseview')->withError('Filed Case Not Found..!!');
        }

        // Validation
        $validator = Validator::make($request->all(), [
            // 'claimant_first_name' => 'required|string|max:100',
            // 'claimant_middle_name' => 'nullable|string|max:100',
            // 'claimant_last_name' => 'nullable|string|max:100',
            // 'claimant_mobile' => 'required|digits:10',
            // 'claimant_email' => 'required|email|max:100',
            // 'claimant_address1' => 'required',
            // 'claimant_address2' => 'nullable',
            // 'claimant_address_type' => 'required',
            // 'claimant_state_id' => 'required|exists:states,id',
            // 'claimant_city_id' => 'required|exists:cities,id',
            // 'claimant_pincode' => 'required',
            // 'respondent_first_name' => 'required|string|max:100',
            // 'respondent_middle_name' => 'nullable|string|max:100',
            // 'respondent_last_name' => 'nullable|string|max:100',
            // 'respondent_mobile' => 'required|digits:10',
            // 'respondent_email' => 'required|email|max:100',
            // 'respondent_address1' => 'required',
            // 'respondent_address2' => 'nullable',
            // 'respondent_address_type' => 'required',
            // 'respondent_state_id' => 'required|exists:states,id',
            // 'respondent_city_id' => 'required|exists:cities,id',
            // 'respondent_pincode' => 'required',
            // 'brief_of_case' => 'required',
            // 'amount_in_dispute' => 'nullable',
            // 'case_type' => 'required',
            // 'language' => 'nullable',
            // 'agreement_exist' => 'nullable',
            'application_form' => 'nullable|max:4096',
            'foreclosure_statement' => 'nullable|max:4096',
            'loan_agreement' => 'nullable|max:4096',
            'account_statement' => 'nullable|max:4096',
            'other_document' => 'nullable|max:4096',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Handle file uploads only if new files are uploaded
        if ($request->hasFile('application_form')) {
            $applicationFormPath = Helper::saveFile($request->file('application_form'), 'organization/casefile');
        } else {
            $applicationFormPath = $caseviewData->application_form; 
        }
        if ($request->hasFile('foreclosure_statement')) {
            $foreclosureStatementPath = Helper::saveFile($request->file('foreclosure_statement'), 'organization/casefile');
        } else {
            $foreclosureStatementPath = $caseviewData->foreclosure_statement; 
        }
        if ($request->hasFile('loan_agreement')) {
            $loanAgreementPath = Helper::saveFile($request->file('loan_agreement'), 'organization/casefile');
        } else {
            $loanAgreementPath = $caseviewData->loan_agreement; 
        }
        if ($request->hasFile('account_statement')) {
            $accountStatementPath = Helper::saveFile($request->file('account_statement'), 'organization/casefile');
        } else {
            $accountStatementPath = $caseviewData->account_statement; 
        }
        if ($request->hasFile('other_document')) {
            $otherDocumentPath = Helper::saveFile($request->file('other_document'), 'organization/casefile');
        } else {
            $otherDocumentPath = $caseviewData->other_document; 
        }

        // Update case data
        $caseviewData->update([
            'usertype'                  => 2,
            'organization_id'           => $organization_authData->id,
            // 'claimant_first_name'       => $request->claimant_first_name,
            // 'claimant_middle_name'      => $request->claimant_middle_name,
            // 'claimant_last_name'        => $request->claimant_last_name,
            // 'claimant_mobile'           => $request->claimant_mobile,
            // 'claimant_email'            => $request->claimant_email,
            // 'claimant_address1'         => $request->claimant_address1,
            // 'claimant_address2'         => $request->claimant_address2,
            // 'claimant_address_type'     => $request->claimant_address_type,
            // 'claimant_state_id'         => $request->claimant_state_id,
            // 'claimant_city_id'          => $request->claimant_city_id,
            // 'claimant_pincode'          => $request->claimant_pincode,
            // 'respondent_first_name'     => $request->respondent_first_name,
            // 'respondent_middle_name'    => $request->respondent_middle_name,
            // 'respondent_last_name'      => $request->respondent_last_name,
            // 'respondent_mobile'         => $request->respondent_mobile,
            // 'respondent_email'          => $request->respondent_email,
            // 'respondent_address1'       => $request->respondent_address1,
            // 'respondent_address2'       => $request->respondent_address2,
            // 'respondent_address_type'   => $request->respondent_address_type,
            // 'respondent_state_id'       => $request->respondent_state_id,
            // 'respondent_city_id'        => $request->respondent_city_id,
            // 'respondent_pincode'        => $request->respondent_pincode,
            // 'brief_of_case'             => $request->brief_of_case,
            // 'amount_in_dispute'         => $request->amount_in_dispute,
            // 'case_type'                 => $request->case_type,
            // 'language'                  => $request->language,
            // 'agreement_exist'           => $request->agreement_exist,
            'application_form'          => $applicationFormPath,
            'foreclosure_statement'     => $foreclosureStatementPath,
            'loan_agreement'            => $loanAgreementPath,
            'account_statement'         => $accountStatementPath,
            'other_document'            => $otherDocumentPath,
        ]);

        return to_route('organization.cases.filecaseview')->withSuccess('Filed Case Documents Upload Successfully..!!');
    }

    //For Upload Notice By Organization
    public function store(Request $request, $id)
    {
        $existing1 = Notice::where('file_case_id', $id)->where('notice_type', 1)->exists();
        $existing2 = Notice::where('file_case_id', $id)->where('notice_type', 2)->exists();

        if ($existing1 || $existing2) {
            return redirect()->back()->with('error', 'Notices already uploaded.');
        }

        $request->validate([
            'notice_first' => 'required|mimes:pdf|max:5120',
            'notice_second' => 'required|mimes:pdf|max:5120',
        ]);

        // First notice (type 1)
        if ($request->hasFile('notice_first')) {
            $noticefirstPath = Helper::saveFile($request->file('notice_first'),'notices');

            Notice::create([
                'file_case_id' => $id,
                'notice_type' => 1,
                'notice' => $noticefirstPath,
                'notice_date' => now(),
                'notice_send_date' => null,
                'email_status' => 0,
                'whatsapp_status' => 0,
                'whatsapp_notice_status' => 0,
                'whatsapp_dispatch_datetime' => null,
            ]);
        }

        // Second notice (type 2)
        if ($request->hasFile('notice_second')) {
            $noticesecondPath = Helper::saveFile($request->file('notice_second'),'notices');

            Notice::create([
                'file_case_id' => $id,
                'notice_type' => 2,
                'notice' => $noticesecondPath,
                'notice_date' => now(),
                'notice_send_date' => null,
                'email_status' => 0,
                'whatsapp_status' => 0,
                'whatsapp_notice_status' => 0,
                'whatsapp_dispatch_datetime' => null,
            ]);
        }

        return redirect()->back()->with('success', 'Both notices uploaded successfully.');
    }



    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new FileCase, $request->id);
    }

    public function filecase(Request $request): View
    {
        $title = 'File Cases';

        $organization = auth('organization')->user();

        if (!$organization) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }

        return view('organization.cases.filecase', compact('organization','title'));
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx,csv|max:2048', 
        ]);

        $organization = auth('organization')->user();
        $organizationId = $organization->id;

        if (!$organizationId) {
            return back()->with('error', 'No organization found.');
        }

        try {
            Log::info('Import function triggered, file received.');
            Excel::import(new FileCaseImport($organizationId), $request->file('file'));
            Log::info('Import process completed.');
            
            return redirect()->route('organization.cases.filecaseview')->with('success', 'File imported successfully.');

        } catch (\Exception $e) {
            Log::error('File import failed: ' . $e->getMessage());
            return back()->with('error', 'File import failed. Please check the format.');
        }
    }

    public function downloadSample()
    {
        return Excel::download(new SampleFileCaseExport, 'sample_file_case.xlsx');
    }

    public function viewcasedetail($id): View|RedirectResponse
    {
        $title = 'Filed Case Detail';
        $organization_authData = auth('organization')->user();
        
        $caseviewData = FileCase::find($id);
        
        if (!$caseviewData) {
            return to_route('cases.filecaseview')->with('error', 'Filed Case Not Found..!!');
        }

         // Get child organization IDs if the logged-in organization is a parent
        $childOrganizations = Organization::where('parent_id', $organization_authData->id)->pluck('id')->toArray();

        // Allowed organization IDs (parent + child organizations)
        $allowedOrganizationIds = array_merge([$organization_authData->id], $childOrganizations);

        // Fetch case data where the organization is either parent or child
        $caseData = FileCase::with([
                'payments', 
                'assignedCases.arbitrator', 
                'assignedCases.advocate', 
                'assignedCases.caseManager', 
                'assignedCases.mediator', 
                'assignedCases.conciliator'
            ])
            ->whereIn('organization_id', $allowedOrganizationIds)
            ->where('id', $caseviewData->id)
            ->where('status', 1)
            ->latest()
            ->get();

        if ($caseData->isEmpty()) {
            return to_route('cases.filecaseview')->with('error', 'You are not authorized to view this case.');
        }
    
        return view('organization.cases.viewcasedetail', compact('caseviewData', 'title', 'organization_authData','caseData'));
    }
    
}