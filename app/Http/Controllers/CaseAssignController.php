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
use PHPUnit\Framework\TestStatus\Notice as TestStatusNotice;

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
        // dd($caseData);
        
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

        // ##############################################
        // Appointment Of Case Manager - 2B - Notice Send
        // ##############################################
        $assigncaseData = AssignCase::where('case_id', $id)->first();
        $noticedataFetchCaseManager = Notice::where('file_case_id', $id)->where('notice_type', 4)->first();
       
        if (!empty($assigncaseData->case_manager_id) && !$noticedataFetchCaseManager){
            $arbitratorIds = explode(',', $assigncaseData->arbitrator_id);
            $arbitratorsName = Drp::whereIn('id', $arbitratorIds)->pluck('name')->implode(', ');
            $casemanagerData = Drp::where('id', $assigncaseData->case_manager_id)->first();

            $noticetemplateData = NoticeTemplate::where('id', 4)->first();
            $noticeTemplate = $noticetemplateData->notice_format;

            // Define your replacement values
            $data = [
                "ARBITRATOR'S NAME" => $arbitratorsName ?? '',
                "CASE MANAGER'S NAME" => $casemanagerData->name ?? '',
                'PHONE NUMBER' => $casemanagerData->mobile ?? '',
                'EMAIL ADDRESS' => ($casemanagerData->address1 ?? '') . '&nbsp;' . ($casemanagerData->address2 ?? ''),

                'CASE REGISTRATION NUMBER' => $caseData->case_number ?? '',
                'BANK/ORGANISATION/CLAIMANT NAME' => ($caseData->claimant_first_name ?? '') . '&nbsp;' . ($caseData->claimant_last_name ?? ''),
                'BANK/ORGANISATION/CLAIMANT REGISTERED ADDRESS' => ($caseData->claimant_address1 ?? '') . '&nbsp;' . ($caseData->claimant_address2 ?? ''),

                'CUSTOMER NAME' => ($caseData->respondent_first_name ?? '') . '&nbsp;' . ($caseData->respondent_last_name ?? ''),
                'CUSTOMER ADDRESS' => ($caseData->respondent_address1 ?? '') . '&nbsp;' . ($caseData->respondent_address2 ?? ''),
                'CUSTOMER MOBILE NO' => $caseData->respondent_mobile ?? '',
                'CUSTOMER MAIL ID' => $caseData->respondent_email ?? '',

                'ARBITRATION CLAUSE NO' => 123456,

                'DATE' => now()->format('d-m-Y'),
                'STAGE 2B NOTICE' => now()->format('d-m-Y'),
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

            $signature = Setting::where('setting_type','1')->get()->pluck('filed_value', 'setting_name')->toArray();
            // Append the signature image at the end of the content, aligned right
            $finalNotice .= '
                <div style="text-align: right; margin-top: 0px;">
                    <img src="' . asset('storage/' . $signature['mediateway_signature']) . '" style="height: 80px;" alt="Signature">
                </div>
            ';

            // 1. Prepare your HTML with custom styles
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
            ' . $finalNotice;

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
                'file_case_id' => $caseData->id,
                'notice_type' => 4,
                'notice' => $savedPath,
                'notice_date' => now(),
                'notice_send_date' => null,
                'email_status' => 0,
                'whatsapp_status' => 0,
                'whatsapp_notice_status' => 0,
                'whatsapp_dispatch_datetime' => null,
            ]);

            //Send Notice for Assign Arbitrator
            $data = Setting::where('setting_type','3')->get()->pluck('filed_value', 'setting_name')->toArray();
            
            Config::set("mail.mailers.smtp", [
                'transport'     => 'smtp',
                'host'          => $data['smtp_host'],
                'port'          => $data['smtp_port'],
                'encryption'    => in_array((int) $data['smtp_port'], [587, 2525]) ? 'tls' : 'ssl',
                'username'      => $data['smtp_user'],
                'password'      => $data['smtp_pass'],
                'timeout'       =>  null,
                'auth_mode'     =>  null,
            ]);

            Config::set("mail.from", [
                'address'       =>  $data['email_from'],
                'name'          =>  config('app.name'),
            ]);

            if (!empty($caseData->respondent_email)) {
                $email = filter_var($caseData->respondent_email, FILTER_SANITIZE_EMAIL);
            
                $validator = Validator::make(['email' => $email], [
                    'email' => 'required|email:rfc,dns',
                ]);
            
                if ($validator->fails()) {

                    Log::warning("Invalid email address: $email");
                    $notice->update(['email_status' => 2]);

                } else {

                    $subject = $noticetemplateData->subject;
                    $description = $noticetemplateData->email_content;
           
                    // Mail::send('emails.simple', compact('subject', 'description'), function ($message) use ($savedPath, $subject, $email) {
                    //     $message->to($email)
                    //             ->subject($subject)
                    //             ->attach(public_path(str_replace('\\', '/', $savedPath)), [
                    //                 'mime' => 'application/pdf',
                    //             ]);
                    // });
            
                    // if (Mail::failures()) {
                    //     Log::error("Failed to send email to: $email");
                    //     $notice->update(['email_status' => 2]);
                    // } else {
                        $notice->update(['notice_send_date' => now()]);
                        $notice->update(['email_status' => 1]);
                    // }
                }
            }
        }

        // ##################################################
        // Final Appointment Of Arbitrator - 3A - Notice Send
        // ##################################################
        $assigncaseData = AssignCase::where('case_id', $id)->first();
        $noticedataFetchArbitrator = Notice::where('file_case_id', $id)->where('notice_type', 5)->first();

        if(($assigncaseData->receiveto_casemanager == 1) && empty($noticedataFetchArbitrator)){
            $arbitratorIds = explode(',', $assigncaseData->arbitrator_id);
            $arbitratorsName = Drp::whereIn('id', $arbitratorIds)->pluck('name')->implode(', ');
            $arbitratorsData = Drp::whereIn('id', $arbitratorIds)->first();

            $casemanagerData = Drp::where('id', $assigncaseData->case_manager_id)->first();
     
            $noticetemplateData = NoticeTemplate::where('id', 5)->first();
            $noticeTemplate = $noticetemplateData->notice_format;
            
            // Define your replacement values
            $data = [
                "ARBITRATOR'S NAME" => $arbitratorsName ?? '',
                "CASE MANAGER'S NAME" => $casemanagerData->name ?? '',
                'PHONE NUMBER' => $casemanagerData->mobile ?? '',
                'EMAIL ADDRESS' => ($casemanagerData->address1 ?? '') . '&nbsp;' . ($casemanagerData->address2 ?? ''),

                'CASE REGISTRATION NUMBER' => $caseData->case_number ?? '',
                'BANK/ORGANISATION/CLAIMANT NAME' => ($caseData->claimant_first_name ?? '') . '&nbsp;' . ($caseData->claimant_last_name ?? ''),
                'BANK/ORGANISATION/CLAIMANT REGISTERED ADDRESS' => ($caseData->claimant_address1 ?? '') . '&nbsp;' . ($caseData->claimant_address2 ?? ''),

                'CLAIM SIGNATORY/AUTHORISED OFFICER MOBILE NO' => $caseData->file_case_details->claim_signatory_authorised_officer_mobile_no ?? '',
                "CLAIM SIGNATORY/AUTHORISED OFFICER'S MAIL ID" => $caseData->file_case_details->claim_signatory_authorised_officer_mail_id ?? '',

                'LOAN NO' => $caseData->loan_number ?? '',
                'AGREEMENT DATE' => $caseData->agreement_date ?? '',
                'FINANCE AMOUNT' => $caseData->file_case_details->finance_amount ?? '',
                'TENURE' => $caseData->file_case_details->tenure ?? '',
                'STAGE 1 NOTICE: LOAN RECALL CUM PREARBITRATION NOTICE' =>  now()->format('d-m-Y'),
                'FORECLOSURE AMOUNT' => $caseData->file_case_details->foreclosure_amount ?? '',

                "ARBITRATOR'S NAME"  => $arbitratorsData->name ?? '',
                "ARBITRATOR'S SPECIALIZATION" => $arbitratorsData->specialization ?? '',
                "ARBITRATOR'S ADDRESS" => ($arbitratorsData->address1 ?? '') . '&nbsp;' . ($arbitratorsData->address2 ?? ''),

                'STAGE 3-A NOTICE: PROPOSAL LETTER FOR APPOINTMENT OF ARBITRATOR' =>  now()->format('d-m-Y'),

                'CUSTOMER NAME' => ($caseData->respondent_first_name ?? '') . '&nbsp;' . ($caseData->respondent_last_name ?? ''),
                'CUSTOMER ADDRESS' => ($caseData->respondent_address1 ?? '') . '&nbsp;' . ($caseData->respondent_address2 ?? ''),
                'CUSTOMER MOBILE NO' => $caseData->respondent_mobile ?? '',
                'CUSTOMER MAIL ID' => $caseData->respondent_email ?? '',

                'ARBITRATION CLAUSE NO' => 123456,

                'DATE' => now()->format('d-m-Y'),
                'STAGE 2B NOTICE' => now()->format('d-m-Y'),
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

            
            $signature = Setting::where('setting_type','1')->get()->pluck('filed_value', 'setting_name')->toArray();
            // Append the signature image at the end of the content, aligned right
            $finalNotice .= '
                <div style="text-align: right; margin-top: 0px;">
                    <img src="' . asset('storage/' . $signature['mediateway_signature']) . '" style="height: 80px;" alt="Signature">
                </div>
            ';

            // 1. Prepare your HTML with custom styles
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
            ' . $finalNotice;

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
                'file_case_id' => $caseData->id,
                'notice_type' => 5,
                'notice' => $savedPath,
                'notice_date' => now(),
                'notice_send_date' => null,
                'email_status' => 0,
                'whatsapp_status' => 0,
                'whatsapp_notice_status' => 0,
                'whatsapp_dispatch_datetime' => null,
            ]);

            //Send Notice for Assign Arbitrator
            $data = Setting::where('setting_type','3')->get()->pluck('filed_value', 'setting_name')->toArray();

            Config::set("mail.mailers.smtp", [
                'transport'     => 'smtp',
                'host'          => $data['smtp_host'],
                'port'          => $data['smtp_port'],
                'encryption'    => in_array((int) $data['smtp_port'], [587, 2525]) ? 'tls' : 'ssl',
                'username'      => $data['smtp_user'],
                'password'      => $data['smtp_pass'],
                'timeout'       =>  null,
                'auth_mode'     =>  null,
            ]);

            Config::set("mail.from", [
                'address'       =>  $data['email_from'],
                'name'          =>  config('app.name'),
            ]);

            if (!empty($caseData->respondent_email)) {
                $email = filter_var($caseData->respondent_email, FILTER_SANITIZE_EMAIL);
            
                $validator = Validator::make(['email' => $email], [
                    'email' => 'required|email:rfc,dns',
                ]);
            
                if ($validator->fails()) {

                    Log::warning("Invalid email address: $email");
                    $notice->update(['email_status' => 2]);

                } else {

                    $subject = $noticetemplateData->subject;
                    $description = $noticetemplateData->email_content;
           
                    // Mail::send('emails.simple', compact('subject', 'description'), function ($message) use ($savedPath, $subject, $email) {
                    //     $message->to($email)
                    //             ->subject($subject)
                    //             ->attach(public_path(str_replace('\\', '/', $savedPath)), [
                    //                 'mime' => 'application/pdf',
                    //             ]);
                    // });
            
                    // if (Mail::failures()) {
                        Log::error("Failed to send email to: $email");
                        $notice->update(['email_status' => 2]);
                    // } else {
                        $notice->update(['notice_send_date' => now()]);
                        $notice->update(['email_status' => 1]);
                    // }
                }
            }
        }

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
