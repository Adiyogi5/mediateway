<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\AssignCase;
use Carbon\Carbon;
use App\Models\Drp;
use App\Models\FileCase;
use App\Models\FileCaseDetail;
use App\Models\Notice;
use App\Models\NoticeTemplate;
use App\Models\Setting;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
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
                    'file_cases.product_type',
                    'file_cases.case_number',
                    'file_cases.loan_number',
                    'file_cases.status',
                    'file_cases.created_at',
                    'assign_cases.sendto_casemanager',
                    'assign_cases.receiveto_casemanager',
                    'assign_cases.confirm_to_arbitrator',
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
            if ($request->filled('status')) {
                $data->where('file_cases.status', $request->status);
            }
            if ($request->filled('send_status')) {
                $data->where('assign_cases.sendto_casemanager', $request->send_status);
            }
            if ($request->filled('receive_status')) {
                $data->where('assign_cases.receiveto_casemanager', $request->receive_status);
            }
            if ($request->filled('arbitrator_status')) {
                $data->where('assign_cases.confirm_to_arbitrator', $request->arbitrator_status);
            }
            if ($request->filled('assigned_status')) {
                if ($request->assigned_status == 1) {
                    $data->whereNotNull('assign_cases.id');
                } else {
                    $data->whereNull('assign_cases.id');
                }
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
                        ? '<small class="badge fw-semi-bold rounded-pill badge-danger">Not Sent</small>'
                        : '<small class="badge fw-semi-bold rounded-pill badge-success">Sent</small>';
                })
                ->addColumn('receive_status', function ($row) {
                    return $row['receiveto_casemanager'] == 0
                        ? '<small class="badge fw-semi-bold rounded-pill badge-danger">Not Received</small>'
                        : '<small class="badge fw-semi-bold rounded-pill badge-success">Received</small>';
                })   
                ->addColumn('arbitrator_status', function ($row) {
                    return $row['confirm_to_arbitrator'] == 0
                        ? '<small class="badge fw-semi-bold rounded-pill badge-danger">Pending</small>'
                        : '<small class="badge fw-semi-bold rounded-pill badge-success">Confirmed</small>';
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
                ->rawColumns(['action', 'status', 'assigned_status', 'send_status', 'receive_status', 'arbitrator_status'])
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
        
        $arbitrators = Drp::where('drp_type', 1)->where('approve_status', 1)->where('status', 1)->get();
        $advocates = Drp::where('drp_type', 2)->where('approve_status', 1)->where('status', 1)->get();
        $casemanagers = Drp::where('drp_type', 3)->where('approve_status', 1)->where('status', 1)->get();
        $mediators = Drp::where('drp_type', 4)->where('approve_status', 1)->where('status', 1)->get();
        $conciliators = Drp::where('drp_type', 5)->where('approve_status', 1)->where('status', 1)->get();

        if (!$caseData) return to_route('caseassign')->withError('Case Not Found..!!');

        return view('caseassign.assign', compact('caseData','assignCase','arbitrators','advocates','casemanagers','mediators','conciliators'));
    }

    

    public function updateassigndetail(Request $request, $id): RedirectResponse
    {
        $caseData = FileCase::with('file_case_details')->find($id);
        
        if (!$caseData) return to_route('caseassign')->withError('Case Not Found..!!');

        $firstnoticeData = Notice::where('file_case_id', $id)->where('notice_type', 1)->first();

        if (empty($firstnoticeData)) {
            return back()->with('error', 'Notice 1 Not Uploaded..!!');
        }

        $data = $request->validate([
            // 'case_id'          => ['required'],
            'arbitrator_id'    => ['nullable'],
            'advocate_id'      => ['nullable'],
            'case_manager_id'  => ['required'],
            'mediator_id'      => ['nullable'],
            'conciliator_id'   => ['nullable'],
        ]);

        AssignCase::updateOrCreate(
            ['case_id' => $id],
            array_merge([
                'case_id'               => $id,
                'arbitrator_id'         => implode(',', $data['arbitrator_id'] ?? []),
                'case_manager_id'       => $data['case_manager_id'] ?? null,
                'conciliator_id'        => $data['conciliator_id'] ?? null,
                'mediator_id'           => $data['mediator_id'] ?? null,
                'advocate_id'           => $data['advocate_id'] ?? null,
                'sendto_casemanager'    => 1,
            ])               
        );

        $assigncaseData = AssignCase::where('case_id', $caseData->id)->first();
        $noticedataFetchArbitrator = Notice::where('file_case_id', $caseData->id)->where('notice_type', 5)->first();
        
            //########## This is Stage 3A Notice for PDF save only for appoint 3 arbitartors ###########
            //##########################################################################################
                if (($assigncaseData->receiveto_casemanager == 0) && empty($noticedataFetchArbitrator)) {
                    $arbitratorIds   = explode(',', $assigncaseData->arbitrator_id);
                    $arbitratorsName = Drp::whereIn('id', $arbitratorIds)->pluck('name')->implode(', ');
                    $arbitratorsData = Drp::whereIn('id', $arbitratorIds)->get();
                    $firstArb  = $arbitratorsData[0] ?? null;
                    $secondArb = $arbitratorsData[1] ?? null;
                    $thirdArb  = $arbitratorsData[2] ?? null;

                    $casemanagerData = Drp::where('id', $assigncaseData->case_manager_id)->first();

                    $noticetemplateData = NoticeTemplate::where('id', 5)->first();
                    $noticeTemplate     = $noticetemplateData->notice_format;

                    $noticedateData = FileCase::with('file_case_details','guarantors')
                        ->join(DB::raw("(
                            SELECT
                                id AS org_id,
                                name AS org_name,
                                IF(parent_id = 0, id, parent_id) AS effective_parent_id,
                                IF(parent_id = 0, name,
                                    (SELECT name FROM organizations AS parent_org WHERE parent_org.id = organizations.parent_id)
                                ) AS effective_parent_name
                            FROM organizations
                        ) AS org_with_parent"), 'org_with_parent.org_id', '=', 'file_cases.organization_id')
                        ->leftJoin(DB::raw("(SELECT * FROM notices WHERE notice_type = 1) AS notice_type1"), 'notice_type1.file_case_id', '=', 'file_cases.id')
                        ->join('organization_lists', 'org_with_parent.effective_parent_name', '=', 'organization_lists.name')
                        ->join('organization_notice_timelines', 'organization_notice_timelines.organization_list_id', '=', 'organization_lists.id')
                        ->where('file_cases.id', $id)
                        ->whereDoesntHave('notices', function ($query) {
                            $query->where('notice_type', 5);
                        })
                        ->whereIn('organization_notice_timelines.notice_3a', function ($query) {
                            $query->select('notice_3a')
                                ->from('organization_notice_timelines')
                                ->whereNull('deleted_at')
                                ->whereRaw('organization_notice_timelines.organization_list_id = organization_lists.id');
                        })
                        ->select(
                            'file_cases.*',
                            'organization_notice_timelines.notice_3a',
                            DB::raw('org_with_parent.effective_parent_id as parent_id'),
                            DB::raw('org_with_parent.effective_parent_name as parent_name'),
                            DB::raw('notice_type1.notice_date as notice_type_1_date')
                        )
                        ->distinct()
                        ->first();
                   
                    $notice3adate = Carbon::parse($noticedateData->notice_type_1_date)
                        ->addDays($noticedateData->notice_3a - 1)
                        ->format('Y-m-d');

                    // Define your replacement values
                    $data = [
                        "ARBITRATOR'S NAME"                                               => $arbitratorsName ?? '',
                        "CASE MANAGER'S NAME"                                             => $casemanagerData->name ?? '',
                        'PHONE NUMBER'                                                    => $casemanagerData->mobile ?? '',
                        'EMAIL ADDRESS'                                                   => ($casemanagerData->address1 ?? '') . '&nbsp;' . ($casemanagerData->address2 ?? ''),

                        'CASE REGISTRATION NUMBER'                                        => $noticedateData->case_number ?? '',
                        'BANK/ORGANISATION/CLAIMANT NAME'                                 => ($noticedateData->claimant_first_name ?? '') . '&nbsp;' . ($noticedateData->claimant_last_name ?? ''),
                        'BANK/ORGANISATION/CLAIMANT REGISTERED ADDRESS'                   => ($noticedateData->claimant_address1 ?? '') . '&nbsp;' . ($noticedateData->claimant_address2 ?? ''),

                        'CLAIM SIGNATORY/AUTHORISED OFFICER NAME'                         => $noticedateData->file_case_details->claim_signatory_authorised_officer_name ?? '',
                        'CLAIM SIGNATORY/AUTHORISED OFFICER MOBILE NO'                    => $noticedateData->file_case_details->claim_signatory_authorised_officer_mobile_no ?? '',
                        "CLAIM SIGNATORY/AUTHORISED OFFICER'S MAIL ID"                    => $noticedateData->file_case_details->claim_signatory_authorised_officer_mail_id ?? '',

                        'LOAN NO'                                                         => $noticedateData->loan_number ?? '',
                        'AGREEMENT DATE'                                                  => $noticedateData->agreement_date ?? '',
                        'FINANCE AMOUNT'                                                  => $noticedateData->file_case_details->finance_amount ?? '',
                        'FORECLOSURE AMOUNT'                                              => $noticedateData->file_case_details->foreclosure_amount ?? '',
                        'FORECLOSURE DATE'                                                => $noticedateData->file_case_details->foreclosure_amount_date ?? '',

                        "FIRST ARBITRATOR'S NAME"                                         => $firstArb->name ?? '',
                        "FIRST ARBITRATOR'S SPECIALIZATION"                               => config('constant.case_type')[$firstArb->specialization] ?? '',
                        "FIRST ARBITRATOR'S ADDRESS"                                      => ($firstArb->address1 ?? '') . '&nbsp;' . ($firstArb->address2 ?? ''),

                        "SECOND ARBITRATOR'S NAME"                                        => $secondArb->name ?? '',
                        "SECOND ARBITRATOR'S SPECIALIZATION"                              => config('constant.case_type')[$secondArb->specialization] ?? '',
                        "SECOND ARBITRATOR'S ADDRESS"                                     => ($secondArb->address1 ?? '') . '&nbsp;' . ($secondArb->address2 ?? ''),

                        "THIRD ARBITRATOR'S NAME"                                         => $thirdArb->name ?? '',
                        "THIRD ARBITRATOR'S SPECIALIZATION"                               => config('constant.case_type')[$thirdArb->specialization] ?? '',
                        "THIRD ARBITRATOR'S ADDRESS"                                      => ($thirdArb->address1 ?? '') . '&nbsp;' . ($thirdArb->address2 ?? ''),

                        'CUSTOMER NAME'                                                   => ($noticedateData->respondent_first_name ?? '') . '&nbsp;' . ($noticedateData->respondent_last_name ?? ''),
                        'CUSTOMER ADDRESS'                                                => ($noticedateData->respondent_address1 ?? '') . '&nbsp;' . ($noticedateData->respondent_address2 ?? ''),
                        'CUSTOMER MOBILE NO'                                              => $noticedateData->respondent_mobile ?? '',
                        'CUSTOMER MAIL ID'                                                => $noticedateData->respondent_email ?? '',

                        'ARBITRATION CLAUSE NO'                                           => $noticedateData->arbitration_clause_no ?? '',
                        'ARBITRATION DATE'                                                => $noticedateData->arbitration_date ?? '',
                        'TENURE'                                                          => $noticedateData->file_case_details->tenure ?? '',
                        'PRODUCT'                                                         => $noticedateData->file_case_details->product ?? '',

                        'GUARANTOR 1 NAME'                                                => $noticedateData->guarantors->guarantor_1_name ?? '',
                        'GUARANTOR 1 MOBILE NO'                                           => $noticedateData->guarantors->guarantor_1_mobile_no ?? '',
                        'GUARANTOR 1 EMAIL ID'                                            => $noticedateData->guarantors->guarantor_1_email_id ?? '',
                        'GUARANTOR 1 ADDRESS'                                             => $noticedateData->guarantors->guarantor_1_address ?? '',
                        'GUARANTOR 1 FATHER NAME'                                         => $noticedateData->guarantors->guarantor_1_father_name ?? '',
                           
                        'GUARANTOR 2 NAME'                                                => $noticedateData->guarantors->guarantor_2_name ?? '',
                        'GUARANTOR 2 MOBILE NO'                                           => $noticedateData->guarantors->guarantor_2_mobile_no ?? '',
                        'GUARANTOR 2 EMAIL ID'                                            => $noticedateData->guarantors->guarantor_2_email_id ?? '',
                        'GUARANTOR 2 ADDRESS'                                             => $noticedateData->guarantors->guarantor_2_address ?? '',
                        'GUARANTOR 2 FATHER NAME'                                         => $noticedateData->guarantors->guarantor_2_father_name ?? '',

                        'GUARANTOR 3 NAME'                                                => $noticedateData->guarantors->guarantor_3_name ?? '',
                        'GUARANTOR 3 MOBILE NO'                                           => $noticedateData->guarantors->guarantor_3_mobile_no ?? '',
                        'GUARANTOR 3 EMAIL ID'                                            => $noticedateData->guarantors->guarantor_3_email_id ?? '',
                        'GUARANTOR 3 ADDRESS'                                             => $noticedateData->guarantors->guarantor_3_address ?? '',
                        'GUARANTOR 3 FATHER NAME'                                         => $noticedateData->guarantors->guarantor_3_father_name ?? '',
                            
                        'GUARANTOR 4 NAME'                                                => $noticedateData->guarantors->guarantor_4_name ?? '',
                        'GUARANTOR 4 MOBILE NO'                                           => $noticedateData->guarantors->guarantor_4_mobile_no ?? '',
                        'GUARANTOR 4 EMAIL ID'                                            => $noticedateData->guarantors->guarantor_4_email_id ?? '',
                        'GUARANTOR 4 ADDRESS'                                             => $noticedateData->guarantors->guarantor_4_address ?? '',
                        'GUARANTOR 4 FATHER NAME'                                         => $noticedateData->guarantors->guarantor_4_father_name ?? '',
                            
                        'GUARANTOR 5 NAME'                                                => $noticedateData->guarantors->guarantor_5_name ?? '',
                        'GUARANTOR 5 MOBILE NO'                                           => $noticedateData->guarantors->guarantor_5_mobile_no ?? '',
                        'GUARANTOR 5 EMAIL ID'                                            => $noticedateData->guarantors->guarantor_5_email_id ?? '',
                        'GUARANTOR 5 ADDRESS'                                             => $noticedateData->guarantors->guarantor_5_address ?? '',
                        'GUARANTOR 5 FATHER NAME'                                         => $noticedateData->guarantors->guarantor_5_father_name ?? '',
                            
                        'GUARANTOR 6 NAME'                                                => $noticedateData->guarantors->guarantor_6_name ?? '',
                        'GUARANTOR 6 MOBILE NO'                                           => $noticedateData->guarantors->guarantor_6_mobile_no ?? '',
                        'GUARANTOR 6 EMAIL ID'                                            => $noticedateData->guarantors->guarantor_6_email_id ?? '',
                        'GUARANTOR 6 ADDRESS'                                             => $noticedateData->guarantors->guarantor_6_address ?? '',
                        'GUARANTOR 6 FATHER NAME'                                         => $noticedateData->guarantors->guarantor_6_father_name ?? '',
                            
                        'GUARANTOR 7 NAME'                                                => $noticedateData->guarantors->guarantor_7_name ?? '',
                        'GUARANTOR 7 MOBILE NO'                                           => $noticedateData->guarantors->guarantor_7_mobile_no ?? '',
                        'GUARANTOR 7 EMAIL ID'                                            => $noticedateData->guarantors->guarantor_7_email_id ?? '',
                        'GUARANTOR 7 ADDRESS'                                             => $noticedateData->guarantors->guarantor_7_address ?? '',
                        'GUARANTOR 7 FATHER NAME'                                         => $noticedateData->guarantors->guarantor_7_father_name ?? '',

                        'DATE'                                                            => now()->format('d-m-Y'),

                        'STAGE 1 NOTICE DATE'                                             => $noticedateData->file_case_details->stage_1_notice_date ?? '',
                        'STAGE 1A NOTICE DATE'                                            => $noticedateData->file_case_details->stage_1a_notice_date ?? '',
                        'STAGE 1B NOTICE DATE'                                            => $noticedateData->file_case_details->stage_1b_notice_date ?? '',
                        'STAGE 2B NOTICE DATE'                                            => $noticedateData->file_case_details->stage_2b_notice_date ?? '',
                        'STAGE 3A NOTICE DATE'                                            => $notice3adate ?? '',
                    ];

                    $replaceSummernotePlaceholders = function ($html, $replacements) {
                        foreach ($replacements as $key => $value) {
                            // Escape key for regex
                            $escapedKey = preg_quote($key, '/');

                            // Split into words
                            $words = preg_split('/\s+/', $escapedKey);

                            // Allow tags or spacing between words
                            $pattern = '/\{\{(?:\s|&nbsp;|<[^>]+>)*' . implode('(?:\s|&nbsp;|<[^>]+>)*', $words) . '(?:\s|&nbsp;|<[^>]+>)*\}\}/iu';

                            // Replace using callback
                            $html = preg_replace_callback($pattern, function () use ($value) {
                                return $value;
                            }, $html);
                        }

                        return $html;
                    };

                    $finalNotice = $replaceSummernotePlaceholders($noticeTemplate, $data);

                    $signature = Setting::where('setting_type', '1')->get()->pluck('filed_value', 'setting_name')->toArray();
                        
                    // Image URLs
                    $headerImg = url('storage/' . $signature['mediateway_letterhead']);
                    $signatureImg = asset('storage/' . $signature['mediateway_signature']);

                    // 🟢 Add header image at the top
                    $headerHtml = '
                        <div style="text-align: center; margin-bottom: 20px;">
                            <img src="' . $headerImg . '" style="width: 100%; max-height: 120px;" alt="Header">
                        </div>
                    ';

                    // Add signature at the bottom
                    $finalNotice .= '
                        <div style="text-align: right; margin-top: 0px;">
                            <img src="' . $signatureImg . '" style="height: 80px;" alt="Signature">
                        </div>
                    ';

                        // Prepare HTML
                        $html = '
                            <style>
                                @page {
                                    size: A4;
                                    margin: 12mm;
                                }
                                body {
                                    font-family: DejaVu Sans, sans-serif;
                                    font-size: 12px;
                                    line-height: 1.4;
                                }
                                p {
                                    margin: 0px 0;
                                    padding: 0;
                                }
                                img {
                                    max-width: 100%;
                                    height: auto;
                                }
                            </style>
                            ' . $headerHtml . $finalNotice;

                    // 2. Generate PDF with A4 paper size
                    $pdf = PDF::loadHTML($html)->setPaper('A4', 'portrait')->setOptions(['isRemoteEnabled' => true]);

                    // Create temporary PDF file
                    $tempPdfPath = tempnam(sys_get_temp_dir(), 'pdf');
                    $pdf->save($tempPdfPath);

                    // Wrap temp file in UploadedFile so it can go through Helper::saveFile
                    $uploadedFile = new \Illuminate\Http\UploadedFile(
                        $tempPdfPath,
                        'notice_' . time() . '.pdf',
                        'application/pdf',
                        null,
                        true
                    );

                    // Save the PDF using your helper
                    $savedPath = Helper::saveFile($uploadedFile, 'notices');

                    $notice = Notice::create([
                        'file_case_id'               => $caseData->id,
                        'notice_type'                => 5,
                        'notice'                     => $savedPath,
                        'notice_date'                => $notice3adate,
                        'notice_send_date'           => null,
                        'email_status'               => 0,
                        'whatsapp_status'            => 0,
                        'whatsapp_notice_status'     => 0,
                        'whatsapp_dispatch_datetime' => null,
                    ]);
                    
                    if ($notice) {
                        FileCaseDetail::where('file_case_id', $notice->file_case_id)
                            ->update([
                                'stage_3a_notice_date' => $notice3adate,
                            ]);
                    }

                }

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
