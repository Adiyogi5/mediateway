<?php

namespace App\Http\Controllers\Drp;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\FileCase;
use App\Models\OrderSheet;
use App\Models\SettlementLetter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use App\Models\Notice;
use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Http\JsonResponse;

class CourtRoomController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:drp');
    }

    public function index(Request $request): View | JsonResponse | RedirectResponse
    {
        $title = 'Court Room List';
        $drp = auth('drp')->user();

        if (!$drp) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }
        if ($drp->drp_type !== 1) {
            return redirect()->route('drp.dashboard')->withError('Unauthorized access.');
        }

        return view('drp.courtroom.courtroomlist', compact('drp','title'));
    }


    public function livecourtroom(Request $request, $user = 1, $arbitrator = 1): View | JsonResponse | RedirectResponse
    {
        $title = 'Live Court Room';
        $drp = auth('drp')->user();

        if (!$drp) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }

        if ($drp->drp_type !== 1) {
            return redirect()->route('drp.dashboard')->withError('Unauthorized access.');
        }

        $caseData = FileCase::with(['file_case_details', 'guarantors'])->find(32);

        $flattenedCaseData = $this->flattenCaseData($caseData);
        // dd($caseData);
        $orderSheetTemplates = OrderSheet::where('status', 1)->where('drp_type', 5)->get();
        $settlementLetterTemplates = SettlementLetter::where('status', 1)->where('drp_type', 5)->get();

        return view('drp.courtroom.livecourtroom', compact(
            'drp',
            'title',
            'caseData',
            'orderSheetTemplates',
            'settlementLetterTemplates',
            'flattenedCaseData'
        ));
    }

    function flattenCaseData($caseData) {
        $flat = [];
    
        foreach ($caseData->toArray() as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $subkey => $subval) {
                    if (is_array($subval)) {
                        foreach ($subval as $subsubkey => $subsubval) {
                            $flatKey = strtolower(preg_replace('/[^a-z0-9]+/', '_', "{$key}_{$subkey}_{$subsubkey}"));
                            $flat[$flatKey] = $subsubval;
                        }
                    } else {
                        $flatKey = strtolower(preg_replace('/[^a-z0-9]+/', '_', "{$key}_{$subkey}"));
                        $flat[$flatKey] = $subval;
                    }
                }
            } else {
                $flatKey = strtolower(preg_replace('/[^a-z0-9]+/', '_', $key));
                $flat[$flatKey] = $value;
            }
        }
    
        return $flat;
    }


    public function saveNotice(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'file_case_id' => 'required|exists:file_cases,id',
            'livemeetingdata' => 'required|string',
            'docType' => 'required|string',
            'tempType' => 'required',
        ]);

        // Generate PDF from textarea data
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
                line-height: 1.6;
            }
            p {
                margin: 0px 0;
                padding: 0;
            }
        </style>
        ' . $request->livemeetingdata;

        // 2. Generate PDF with A4 paper size
        $pdf = PDF::loadHTML($html)->setPaper('A4', 'portrait');

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
            'file_case_id' => $request->file_case_id,
            'notice_type' => 1,
            'notice' => $savedPath,
            'notice_date' => now(),
            'notice_send_date' => null,
            'email_status' => 0,
            'whatsapp_status' => 0,
            'whatsapp_notice_status' => 0,
            'whatsapp_dispatch_datetime' => null,
        ]);

        //Send Mail Using SMTP
        $caseData = FileCase::with(['file_case_details', 'guarantors'])->find($request->file_case_id);
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
                // Convert docType to StudlyCase (e.g. 'ordersheet' => 'OrderSheet')
                $modelName = Str::studly($request->docType);
               
                // Build the full model class name with namespace
                $modelClass = "App\\Models\\{$modelName}";

                // Then fetch the data dynamically
                $emailData = $modelClass::where('id', $request->tempType)->first();

                $subject = $emailData->subject;
                $description = $emailData->email_content;
        
                Mail::send('emails.simple', compact('subject', 'description'), function ($message) use ($savedPath, $subject, $email) {
                    $message->to($email)
                            ->subject($subject)
                            ->attach(public_path(str_replace('\\', '/', $savedPath)), [
                                'mime' => 'application/pdf',
                            ]);
                });
        
                if (Mail::failures()) {
                    Log::error("Failed to send email to: $email");
                    $notice->update(['email_status' => 2]);
                } else {
                    $notice->update(['notice_send_date' => now()]);
                    $notice->update(['email_status' => 1]);
                }
            }
        }

        return back()->withSuccess('Notice saved successfully.');
    }

    
}