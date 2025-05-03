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

        $caseData = FileCase::select('file_cases.*','drps.name as arbitrator_name')->with(['file_case_details', 'guarantors'])
                ->join('assign_cases','assign_cases.case_id','=','file_cases.id')
                ->join('drps','drps.id','=','assign_cases.arbitrator_id')
                ->find(32);
       
        $flattenedCaseData = $this->flattenCaseData($caseData);
        // dd($flattenedCaseData);
        $orderSheetTemplates = OrderSheet::where('status', 1)->where('drp_type', 1)->get();
        $settlementLetterTemplates = SettlementLetter::where('status', 1)->where('drp_type', 1)->get();

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
                        // Flatten 2nd-level arrays (e.g., array of fields inside a hasOne relationship)
                        foreach ($subval as $subsubkey => $subsubval) {
                            $flatKey = strtolower(preg_replace('/[^a-z0-9]+/', '_', $subsubkey));
                            $flat[$flatKey] = $subsubval;
                        }
                    } else {
                        // Flatten 1st-level arrays
                        $flatKey = strtolower(preg_replace('/[^a-z0-9]+/', '_', $subkey));
                        $flat[$flatKey] = $subval;
                    }
                }
            } else {
                // Non-array value, just add it directly
                $flatKey = strtolower(preg_replace('/[^a-z0-9]+/', '_', $key));
                $flat[$flatKey] = $value;
            }
        }
        return $flat;
    }


    public function saveNotice(Request $request)
    {
        $request->validate([
            'file_case_id' => 'required|exists:file_cases,id',
            'livemeetingdata' => 'required|string',
            'docType' => 'required|string',
            'tempType' => 'required',
        ]);

        // Set notice type based on tempType
        if($request->docType == 'ordersheet')
        {    
            $noticeType = $request->tempType == 1 ? 9 : 10;

            $noticeexistData = Notice::where('file_case_id', $request->file_case_id)
                                    ->where('notice_type', $noticeType)->first();
           
            if (empty($noticeexistData)) {
                // Get signature settings
                $signature = Setting::where('setting_type', '1')->get()->pluck('filed_value', 'setting_name')->toArray();

                // Generate HTML with styles and content
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
                        margin: 0;
                        padding: 0;
                    }
                    img {
                        max-width: 100%;
                        height: auto;
                    }
                </style>
                ' . $request->livemeetingdata;

                // Append signature image
                if (!empty($signature['mediateway_signature'])) {
                    $html .= '
                        <div style="text-align: right; margin-top: 20px;">
                            <img src="' . asset('storage/' . $signature['mediateway_signature']) . '" style="height: 80px;" alt="Signature">
                        </div>';
                }

                // Generate PDF
                $pdf = PDF::loadHTML($html)->setPaper('A4', 'portrait')->setOptions(['isRemoteEnabled' => true]);

                // Save temporary PDF
                $tempPdfPath = tempnam(sys_get_temp_dir(), 'pdf');
                $pdf->save($tempPdfPath);

                // Wrap temp file in UploadedFile
                $uploadedFile = new \Illuminate\Http\UploadedFile(
                    $tempPdfPath,
                    'notice_' . time() . '.pdf',
                    'application/pdf',
                    null,
                    true
                );

                // Save file using helper
                $savedPath = Helper::saveFile($uploadedFile, 'notices');
            
                Notice::create([
                    'file_case_id' => $request->file_case_id,
                    'notice_type' => $noticeType,
                    'notice' => $savedPath,
                    'notice_date' => now(),
                    'notice_send_date' => null,
                    'email_status' => 0,
                    'whatsapp_status' => 0,
                    'whatsapp_notice_status' => 0,
                    'whatsapp_dispatch_datetime' => null,
                ]);
                
                return back()->withSuccess('Notice saved successfully.');
            } else {
                return back()->with('error', 'Notice already exists for this Case.');
            }    
        }

        elseif($request->docType == 'settlementletter')
        {
            $noticeType = $request->tempType == 1 ? 11 : 12;

            $noticeexistData = Notice::where('file_case_id', $request->file_case_id)
                                    ->where('notice_type', $noticeType)->first();
            
            if (empty($noticeexistData)) {
                // Get signature settings
                $signature = Setting::where('setting_type', '1')->get()->pluck('filed_value', 'setting_name')->toArray();

                // Generate HTML with styles and content
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
                        margin: 0;
                        padding: 0;
                    }
                    img {
                        max-width: 100%;
                        height: auto;
                    }
                </style>
                ' . $request->livemeetingdata;

                // Append signature image
                if (!empty($signature['mediateway_signature'])) {
                    $html .= '
                        <div style="text-align: right; margin-top: 20px;">
                            <img src="' . asset('storage/' . $signature['mediateway_signature']) . '" style="height: 80px;" alt="Signature">
                        </div>';
                }

                // Generate PDF
                $pdf = PDF::loadHTML($html)->setPaper('A4', 'portrait')->setOptions(['isRemoteEnabled' => true]);

                // Save temporary PDF
                $tempPdfPath = tempnam(sys_get_temp_dir(), 'pdf');
                $pdf->save($tempPdfPath);

                // Wrap temp file in UploadedFile
                $uploadedFile = new \Illuminate\Http\UploadedFile(
                    $tempPdfPath,
                    'notice_' . time() . '.pdf',
                    'application/pdf',
                    null,
                    true
                );

                // Save file using helper
                $savedPath = Helper::saveFile($uploadedFile, 'notices');
            
                Notice::create([
                    'file_case_id' => $request->file_case_id,
                    'notice_type' => $noticeType,
                    'notice' => $savedPath,
                    'notice_date' => now(),
                    'notice_send_date' => null,
                    'email_status' => 0,
                    'whatsapp_status' => 0,
                    'whatsapp_notice_status' => 0,
                    'whatsapp_dispatch_datetime' => null,
                ]);
                return back()->withSuccess('Notice saved successfully.');
            } else {
                return back()->with('error', 'Notice already exists for this Case.');
            }   
        }
    }

    
}