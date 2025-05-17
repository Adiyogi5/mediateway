<?php

namespace App\Http\Controllers\Drp;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\CourtRoom;
use App\Models\FileCase;
use App\Models\OrderSheet;
use App\Models\SettlementLetter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use App\Models\Notice;
use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CaseManagerCourtRoomController extends Controller
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
        if ($drp->drp_type !== 3) {
            return redirect()->route('drp.dashboard')->withError('Unauthorized access.');
        }
        
        $courtRoomLiveUpcoming = CourtRoom::select(
                'court_rooms.*',
                'drps.name as case_manager_name',
                DB::raw('GROUP_CONCAT(DISTINCT individuals.name SEPARATOR ", ") as individual_name'),
                DB::raw('GROUP_CONCAT(DISTINCT organizations.name SEPARATOR ", ") as organization_name'),
                DB::raw('GROUP_CONCAT(DISTINCT file_cases.case_number SEPARATOR ", ") as case_numbers'),
                DB::raw('GROUP_CONCAT(DISTINCT file_cases.id SEPARATOR ", ") as case_ids')
            )
            ->leftJoin('drps', 'drps.id', '=', 'court_rooms.case_manager_id')
            ->leftJoin('individuals', function ($join) {
                $join->on(DB::raw("FIND_IN_SET(individuals.id, court_rooms.individual_id)"), '>', DB::raw('0'));
            })
            ->leftJoin('organizations', function ($join) {
                $join->on(DB::raw("FIND_IN_SET(organizations.id, court_rooms.organization_id)"), '>', DB::raw('0'));
            })
            ->leftJoin('file_cases', function ($join) {
                $join->on(DB::raw("FIND_IN_SET(file_cases.individual_id, court_rooms.individual_id)"), '>', DB::raw('0'))
                    ->orOn(DB::raw('FIND_IN_SET(file_cases.id, court_rooms.court_room_case_id)'), '>', DB::raw('0'));
            })
            ->where('court_rooms.case_manager_id', $drp->id)
            ->where(function ($query) {
                $query->where('court_rooms.date', '>', Carbon::today()->toDateString())
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('court_rooms.date', Carbon::today()->toDateString())
                                ->where('court_rooms.time', '>=', Carbon::now()->format('H:i:s'));
                    })
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('court_rooms.date', Carbon::today()->toDateString())
                                ->where('court_rooms.status', 1);
                    });
            })
            ->groupBy(
                'court_rooms.id', 
                'court_rooms.court_room_case_id',
                'court_rooms.room_id',
                'court_rooms.date',
                'court_rooms.time',
                'court_rooms.hearing_type',
                'court_rooms.case_manager_id',
                'court_rooms.recording_url',
                'court_rooms.arbitrator_id',
                'court_rooms.individual_id',
                'court_rooms.organization_id',
                'court_rooms.send_mail_to_respondent',
                'court_rooms.email_send_date',
                'court_rooms.send_whatsapp_to_respondent',
                'court_rooms.whatsapp_dispatch_datetime',
                'court_rooms.status',
                'court_rooms.deleted_at',
                'court_rooms.created_at',
                'court_rooms.updated_at',
                'drps.name',
            )
            ->get();
        

        $courtRoomLiveClosed = CourtRoom::select(
                'court_rooms.*',
                'drps.name as case_manager_name',
                DB::raw('GROUP_CONCAT(DISTINCT individuals.name SEPARATOR ", ") as individual_name'),
                DB::raw('GROUP_CONCAT(DISTINCT organizations.name SEPARATOR ", ") as organization_name'),
                DB::raw('GROUP_CONCAT(DISTINCT file_cases.case_number SEPARATOR ", ") as case_numbers'),
                DB::raw('GROUP_CONCAT(DISTINCT file_cases.id SEPARATOR ", ") as case_ids')
            )
            ->leftJoin('drps', 'drps.id', '=', 'court_rooms.case_manager_id')
            ->leftJoin('individuals', function ($join) {
                $join->on(DB::raw("FIND_IN_SET(individuals.id, court_rooms.individual_id)"), '>', DB::raw('0'));
            })
            ->leftJoin('organizations', function ($join) {
                $join->on(DB::raw("FIND_IN_SET(organizations.id, court_rooms.organization_id)"), '>', DB::raw('0'));
            })
            ->leftJoin('file_cases', function ($join) {
                $join->on(DB::raw("FIND_IN_SET(file_cases.individual_id, court_rooms.individual_id)"), '>', DB::raw('0'))
                    ->orOn(DB::raw('FIND_IN_SET(file_cases.id, court_rooms.court_room_case_id)'), '>', DB::raw('0'));
            })
            ->where('court_rooms.case_manager_id', $drp->id)
            ->where('court_rooms.status', 0)
            ->where(function ($query) {
                $query->where('court_rooms.date', '<', Carbon::today()->toDateString())
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('court_rooms.date', Carbon::today()->toDateString())
                                ->where('court_rooms.time', '<=', Carbon::now()->format('H:i:s'));
                    });
            })
            ->groupBy(
                'court_rooms.id', 
                'court_rooms.court_room_case_id',
                'court_rooms.room_id',
                'court_rooms.date',
                'court_rooms.time',
                'court_rooms.hearing_type',
                'court_rooms.case_manager_id',
                'court_rooms.recording_url',
                'court_rooms.arbitrator_id',
                'court_rooms.individual_id',
                'court_rooms.organization_id',
                'court_rooms.send_mail_to_respondent',
                'court_rooms.email_send_date',
                'court_rooms.send_whatsapp_to_respondent',
                'court_rooms.whatsapp_dispatch_datetime',
                'court_rooms.status',
                'court_rooms.deleted_at',
                'court_rooms.created_at',
                'court_rooms.updated_at',
                'drps.name',
            )
            ->get();

        $upcomingRooms = $courtRoomLiveUpcoming;
        $closedRooms = $courtRoomLiveClosed;
        
        return view('drp.casemanagercourtroom.courtroomlist', compact('drp','title','upcomingRooms','closedRooms'));
    }


    public function livecourtroom(Request $request, $room_id): View | JsonResponse | RedirectResponse
    {
        $title = 'Live Court Room';
        $drp = auth('drp')->user();

        if (!$drp) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }

        if ($drp->drp_type !== 3) {
            return redirect()->route('drp.dashboard')->withError('Unauthorized access.');
        }

        $caseIds = explode(',', $request->query('case_ids'));

          // Fetch the case data with all joins and relationships
        $caseData = FileCase::select('file_cases.*', 'drps.name as case_manager_name')
            ->with(['file_case_details', 'guarantors'])
            ->join('assign_cases', 'assign_cases.case_id', '=', 'file_cases.id')
            ->join('drps', 'drps.id', '=', 'assign_cases.case_manager_id')
            ->whereIn('file_cases.id', $caseIds) // Use whereIn instead of find()
            ->get();
       
        $flattenedCaseData = $this->flattenCasemanagerCaseData($caseData);
     
        $orderSheetTemplates = OrderSheet::where('status', 1)->where('drp_type', 1)->get();
        $settlementLetterTemplates = SettlementLetter::where('status', 1)->where('drp_type', 1)->get();
        
        //ZegoCloud Service---------------------
        $localUserID = $drp->slug; // case_manager
        $remoteUserID = $caseIds; // Individual
        $roomID = $room_id;
     
        $zegoToken = $this->generateZegoToken($localUserID, $drp->name);

        return view('drp.casemanagercourtroom.livecourtroom', compact(
            'drp',
            'title',
            'caseData',
            'orderSheetTemplates',
            'settlementLetterTemplates',
            'flattenedCaseData',
            'localUserID',
            'remoteUserID',
            'roomID',
            'zegoToken'
        ));
    }

    public function generateZegoToken($userID)
    {
        $appId = env('ZEGO_APP_ID');
        $serverSecret = env('ZEGO_SERVER_SECRET');
        $expirationTime = 3600; // Token valid for 1 hour

        return $this->createZegoToken($appId, $serverSecret, $userID, $expirationTime);
    }
    
    private function createZegoToken($appId, $serverSecret, $userId, $expiration = 3600)
    {
        $currentTime = time();
        $payload = [
            "app_id" => (int)$appId,
            "user_id" => (string)$userId,
            "nonce" => rand(100000, 999999),
            "ctime" => $currentTime,
            "expire" => $expiration,
        ];
    
        return JWT::encode($payload, $serverSecret, 'HS256');
    }
    
    public function getFlattenedCasemanagerCaseData($caseId)
    {
        $caseData = FileCase::select('file_cases.*', 'drps.name as case_manager_name')
            ->with(['file_case_details', 'guarantors'])
            ->join('assign_cases', 'assign_cases.case_id', '=', 'file_cases.id')
            ->join('drps', 'drps.id', '=', 'assign_cases.case_manager_id')
            ->where('file_cases.id', $caseId)
            ->first();
        
        if ($caseData) {
            $flattenedCaseData = $this->flattenCasemanagerCaseData(collect([$caseData]));
            return response()->json($flattenedCaseData, 200);
        }

        return response()->json(['error' => 'Case not found'], 404);
    }

    public function fetchNoticesByCaseId(Request $request)
    {
        $caseId = $request->case_id;
        $notices = Notice::where('file_case_id', $caseId)
            ->whereIn('notice_type', [1,2,3,4,5,6,7,8,9,10])
            // ->where('email_status', 1)
            ->get();

        return response()->json($notices);
    }

     public function fetchAwardsByCaseId(Request $request)
    {
        $caseId = $request->case_id;
        $notices = Notice::where('file_case_id', $caseId)
            ->whereIn('notice_type', [11,12,13,14,15,16,17,18,19,20,21,22,23,24,25])
            // ->where('email_status', 1)
            ->get();

        return response()->json($notices);
    }

    function flattenCasemanagerCaseData($caseData) {
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