<?php

namespace App\Http\Controllers\Drp;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\FileCase;
use App\Models\MediatorMeetingRoom;
use App\Models\Notice;
use App\Models\OrderSheet;
use App\Models\SettlementLetter;
use Illuminate\Http\RedirectResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\View\View;
use \Yajra\Datatables\Datatables;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MediatorMeetingRoomController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:drp');
    }

    public function index(Request $request): View | JsonResponse | RedirectResponse
    {
        $title = 'Meeting Room List';
        $drp = auth('drp')->user();

        if (!$drp) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }
        if ($drp->approve_status !== 1) {
            return redirect()->route('drp.dashboard')->withError('DRP is Not Approved by Mediateway.');
        }
        if ($drp->drp_type !== 4) {
            return redirect()->route('drp.dashboard')->withError('Unauthorized access.');
        }

        $mediatormeetingroomLiveUpcoming = MediatorMeetingRoom::select(
                'mediator_meeting_rooms.*',
                'drps.name as mediator_name',
                DB::raw('GROUP_CONCAT(DISTINCT file_cases.case_number SEPARATOR ", ") as case_numbers'),
                DB::raw('GROUP_CONCAT(DISTINCT file_cases.id SEPARATOR ", ") as case_ids')
            )
            ->leftJoin('drps', 'drps.id', '=', 'mediator_meeting_rooms.mediator_id')
            ->leftJoin('file_cases', function ($join) {
                $join->On(DB::raw('FIND_IN_SET(file_cases.id, mediator_meeting_rooms.meeting_room_case_id)'), '>', DB::raw('0'));
            })
            ->where('mediator_meeting_rooms.mediator_id', $drp->id)
            ->where(function ($query) {
                $query->where('mediator_meeting_rooms.date', '>', Carbon::today()->toDateString())
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('mediator_meeting_rooms.date', Carbon::today()->toDateString())
                                ->where('mediator_meeting_rooms.time', '>=', Carbon::now()->format('H:i:s'));
                    })
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('mediator_meeting_rooms.date', Carbon::today()->toDateString())
                                ->where('mediator_meeting_rooms.status', 1);
                    });
            })
            ->groupBy(
                    'mediator_meeting_rooms.id', 
                    'mediator_meeting_rooms.meeting_room_case_id',
                    'mediator_meeting_rooms.room_id',
                    'mediator_meeting_rooms.date',
                    'mediator_meeting_rooms.time',
                    'mediator_meeting_rooms.mediator_id',
                    'mediator_meeting_rooms.recording_url',
                    'mediator_meeting_rooms.send_mail_to_respondent',
                    'mediator_meeting_rooms.email_send_date',
                    'mediator_meeting_rooms.send_whatsapp_to_respondent',
                    'mediator_meeting_rooms.whatsapp_dispatch_datetime',
                    'mediator_meeting_rooms.status',
                    'mediator_meeting_rooms.deleted_at',
                    'mediator_meeting_rooms.created_at',
                    'mediator_meeting_rooms.updated_at',
                    'drps.name'
                )
        ->get();

        $mediatormeetingroomLiveClosed = MediatorMeetingRoom::select(
                'mediator_meeting_rooms.*',
                'drps.name as mediator_name',
                DB::raw('GROUP_CONCAT(DISTINCT file_cases.case_number SEPARATOR ", ") as case_numbers'),
                DB::raw('GROUP_CONCAT(DISTINCT file_cases.id SEPARATOR ", ") as case_ids')
            )
            ->leftJoin('drps', 'drps.id', '=', 'mediator_meeting_rooms.mediator_id')
            ->leftJoin('file_cases', function ($join) {
                $join->On(DB::raw('FIND_IN_SET(file_cases.id, mediator_meeting_rooms.meeting_room_case_id)'), '>', DB::raw('0'));
            })
            ->where('mediator_meeting_rooms.mediator_id', $drp->id)
            ->where('mediator_meeting_rooms.status', 0)
            ->where(function ($query) {
                $query->where('mediator_meeting_rooms.date', '<', Carbon::today()->toDateString())
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('mediator_meeting_rooms.date', Carbon::today()->toDateString())
                                ->where('mediator_meeting_rooms.time', '<=', Carbon::now()->format('H:i:s'));
                    });
            })
            ->groupBy(
                'mediator_meeting_rooms.id', 
                'mediator_meeting_rooms.meeting_room_case_id',
                'mediator_meeting_rooms.room_id',
                'mediator_meeting_rooms.date',
                'mediator_meeting_rooms.time',
                'mediator_meeting_rooms.mediator_id',
                'mediator_meeting_rooms.recording_url',
                'mediator_meeting_rooms.send_mail_to_respondent',
                'mediator_meeting_rooms.email_send_date',
                'mediator_meeting_rooms.send_whatsapp_to_respondent',
                'mediator_meeting_rooms.whatsapp_dispatch_datetime',
                'mediator_meeting_rooms.status',
                'mediator_meeting_rooms.deleted_at',
                'mediator_meeting_rooms.created_at',
                'mediator_meeting_rooms.updated_at',
                'drps.name'
            )
        ->get();

        $upcomingroomCount = $mediatormeetingroomLiveUpcoming->count();
        $closedroomCount = $mediatormeetingroomLiveClosed->count();

        $upcomingRooms = $mediatormeetingroomLiveUpcoming;
        $closedRooms = $mediatormeetingroomLiveClosed;

        return view('drp.mediatormeetingroom.mediatormeetingroomlist', compact('drp','title','upcomingRooms','closedRooms','upcomingroomCount','closedroomCount'));
    }

    public function upcomingRoomsData()
    {
        $drp = auth('drp')->user();

        $upcomingRooms = MediatorMeetingRoom::select(
                'mediator_meeting_rooms.*',
                'drps.name as mediator_name',
                DB::raw('GROUP_CONCAT(DISTINCT file_cases.case_number SEPARATOR ", ") as case_numbers'),
                DB::raw('GROUP_CONCAT(DISTINCT file_cases.id SEPARATOR ", ") as case_ids')
            )
            ->leftJoin('drps', 'drps.id', '=', 'mediator_meeting_rooms.mediator_id')
            ->leftJoin('file_cases', function ($join) {
                $join->On(DB::raw('FIND_IN_SET(file_cases.id, mediator_meeting_rooms.meeting_room_case_id)'), '>', DB::raw('0'));
            })
            ->where('mediator_meeting_rooms.mediator_id', $drp->id)
            ->where(function ($query) {
                $query->where('mediator_meeting_rooms.date', '>', Carbon::today()->toDateString())
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('mediator_meeting_rooms.date', Carbon::today()->toDateString())
                                ->where('mediator_meeting_rooms.time', '>=', Carbon::now()->format('H:i:s'));
                    })
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('mediator_meeting_rooms.date', Carbon::today()->toDateString())
                                ->where('mediator_meeting_rooms.status', 1);
                    });
            })
            ->groupBy(
                    'mediator_meeting_rooms.id', 
                    'mediator_meeting_rooms.meeting_room_case_id',
                    'mediator_meeting_rooms.room_id',
                    'mediator_meeting_rooms.date',
                    'mediator_meeting_rooms.time',
                    'mediator_meeting_rooms.mediator_id',
                    'mediator_meeting_rooms.recording_url',
                    'mediator_meeting_rooms.send_mail_to_respondent',
                    'mediator_meeting_rooms.email_send_date',
                    'mediator_meeting_rooms.send_whatsapp_to_respondent',
                    'mediator_meeting_rooms.whatsapp_dispatch_datetime',
                    'mediator_meeting_rooms.status',
                    'mediator_meeting_rooms.deleted_at',
                    'mediator_meeting_rooms.created_at',
                    'mediator_meeting_rooms.updated_at',
                    'drps.name'
                )
        ->get();

        return datatables()->of($upcomingRooms)
            ->addColumn('case_numbers', function ($room) {
                $main = Str::before($room->case_numbers, ',') ?? '-';
                if (Str::contains($room->case_numbers, ',')) {
                    $list = '<ul>';
                    foreach (explode(',', $room->case_numbers) as $case) {
                        $list .= "<li>$case</li>";
                    }
                    $list .= '</ul>';
                    $main .= ' <i class="fa fa-info-circle text-primary ml-2 info-icon" data-bs-toggle="popover" data-bs-html="true" data-bs-content="' . e($list) . '"></i>';
                }
                return $main;
            })
            ->addColumn('date', fn($room) => \Carbon\Carbon::parse($room->date)->format('d F Y'))
            ->addColumn('time', fn($room) => \Carbon\Carbon::parse($room->time)->format('h:i A'))
            ->addColumn('status', fn($room) => $room->status == 1 ? '<span class="fa fa-check pl-3"></span>' : '<span class="fa fa-clock pl-3"></span>')
            ->addColumn('action', function ($room) {
                if ($room->status == 1) {
                    return '<a href="' . route('drp.mediatormeetingroom.livemediatormeetingroom', $room->room_id) . '?case_ids=' . $room->case_ids . '" class="fa fa-video btn bg-success text-white fs-6"></a>';
                } else {
                    return '<span class="fa fa-video btn bg-secondary text-white fs-6" style="cursor:not-allowed;"></span>';
                }
            })
            ->rawColumns(['case_numbers', 'status', 'action'])
            ->make(true);
    }

    public function closedRoomsData()
    {
        $drp = auth('drp')->user();

        $closedRooms = MediatorMeetingRoom::select(
                'mediator_meeting_rooms.*',
                'drps.name as mediator_name',
                DB::raw('GROUP_CONCAT(DISTINCT file_cases.case_number SEPARATOR ", ") as case_numbers'),
                DB::raw('GROUP_CONCAT(DISTINCT file_cases.id SEPARATOR ", ") as case_ids')
            )
            ->leftJoin('drps', 'drps.id', '=', 'mediator_meeting_rooms.mediator_id')
            ->leftJoin('file_cases', function ($join) {
                $join->On(DB::raw('FIND_IN_SET(file_cases.id, mediator_meeting_rooms.meeting_room_case_id)'), '>', DB::raw('0'));
            })
            ->where('mediator_meeting_rooms.mediator_id', $drp->id)
            ->where('mediator_meeting_rooms.status', 0)
            ->where(function ($query) {
                $query->where('mediator_meeting_rooms.date', '<', Carbon::today()->toDateString())
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('mediator_meeting_rooms.date', Carbon::today()->toDateString())
                                ->where('mediator_meeting_rooms.time', '<=', Carbon::now()->format('H:i:s'));
                    });
            })
            ->groupBy(
                'mediator_meeting_rooms.id', 
                'mediator_meeting_rooms.meeting_room_case_id',
                'mediator_meeting_rooms.room_id',
                'mediator_meeting_rooms.date',
                'mediator_meeting_rooms.time',
                'mediator_meeting_rooms.mediator_id',
                'mediator_meeting_rooms.recording_url',
                'mediator_meeting_rooms.send_mail_to_respondent',
                'mediator_meeting_rooms.email_send_date',
                'mediator_meeting_rooms.send_whatsapp_to_respondent',
                'mediator_meeting_rooms.whatsapp_dispatch_datetime',
                'mediator_meeting_rooms.status',
                'mediator_meeting_rooms.deleted_at',
                'mediator_meeting_rooms.created_at',
                'mediator_meeting_rooms.updated_at',
                'drps.name'
            )
        ->get();

        return datatables()->of($closedRooms)
            ->addColumn('case_numbers', function ($room) {
                $main = Str::before($room->case_numbers, ',') ?? '-';
                if (Str::contains($room->case_numbers, ',')) {
                    $list = '<ul>';
                    foreach (explode(',', $room->case_numbers) as $case) {
                        $list .= "<li>$case</li>";
                    }
                    $list .= '</ul>';
                    $main .= ' <i class="fa fa-info-circle text-primary ml-2 info-icon" data-bs-toggle="popover" data-bs-html="true" data-bs-content="' . e($list) . '"></i>';
                }
                return $main;
            })
            ->addColumn('date', fn($room) => \Carbon\Carbon::parse($room->date)->format('d F Y'))
            ->addColumn('time', fn($room) => \Carbon\Carbon::parse($room->time)->format('h:i A'))
            ->addColumn('status', fn($room) => $room->status == 1 ? '<span class="fa fa-check pl-3"></span>' : '<span class="fa fa-clock pl-3"></span>')
            ->addColumn('recording', function ($room) {
                if ($room->recording_url) {
                    return '<video controls width="300"><source src="' . asset('storage/' . $room->recording_url) . '" type="video/mp4"></video>';
                }
                return 'No recording available';
            })
            ->addColumn('action', fn($room) => '<button class="fa fa-handshake btn bg-secondary text-white"></button>')
            ->rawColumns(['case_numbers', 'status', 'recording', 'action'])
            ->make(true);
    }

    // ########### Show case list according to assigned mediator #############
    public function caseList(Request $request)
    {
        $drp = auth('drp')->user();

        if (!$drp) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $cases = FileCase::select('file_cases.*', 'drps.name as mediator_name')
            ->join('assign_cases', 'assign_cases.case_id', '=', 'file_cases.id')
            ->join('drps', 'drps.id', '=', 'assign_cases.mediator_id')
            ->where('drps.id', $drp->id)
            ->get();

        return response()->json(['data' => $cases]);
    }

    // ############# meeting create by mediator ##############
    public function store(Request $request): JsonResponse
    {
       $request->validate([
            'case_ids' => 'required|array|min:1',
            'date' => 'required|date|after_or_equal:today',
            'time' => ['required', function ($attribute, $value, $fail) use ($request) {
                if ($request->date === now()->format('Y-m-d') && $value < now()->format('H:i')) {
                    $fail('The time must not be in the past.');
                }
            }],
        ]);

        $room_id_prefix = 'ORG-MEETING';
        $lastRoom = MediatorMeetingRoom::where('room_id', 'like', $room_id_prefix . '-%')
            ->orderBy('id', 'desc')->first();
        $nextNumber = $lastRoom ? ((int) str_replace($room_id_prefix . '-', '', $lastRoom->room_id) + 1) : 1;
        $room_id = $room_id_prefix . '-' . str_pad($nextNumber, 7, '0', STR_PAD_LEFT);

        MediatorMeetingRoom::create([
            'room_id' => $room_id,
            'meeting_room_case_id' => implode(',', $request->case_ids),
            'mediator_id' => auth('drp')->id(),
            'date' => $request->date,
            'time' => $request->time,
            'status' => 0 // you can adjust based on your logic
        ]);

        return response()->json(['success' => true, 'room_id' => $room_id]);
    }


    public function livemediatormeetingroom(Request $request, $room_id): View | JsonResponse | RedirectResponse
    {
        $title = 'Live Meeting Room';
        $drp = auth('drp')->user();

        if (!$drp) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }
        if ($drp->approve_status !== 1) {
            return redirect()->route('drp.dashboard')->withError('DRP is Not Approved by Mediateway.');
        }
        if ($drp->drp_type !== 4) {
            return redirect()->route('drp.dashboard')->withError('Unauthorized access.');
        }

        $caseIds = explode(',', $request->query('case_ids'));

        // Fetch the case data with all joins and relationships
        $caseData = FileCase::select('file_cases.*', 'drps.name as mediator_name')
            ->with(['file_case_details', 'guarantors'])
            ->join('assign_cases', 'assign_cases.case_id', '=', 'file_cases.id')
            ->join('drps', 'drps.id', '=', 'assign_cases.mediator_id')
            ->whereIn('file_cases.id', $caseIds) // Use whereIn instead of find()
            ->get();
       
        $flattenedCaseData = $this->flattenCaseData($caseData);

        $orderSheetTemplates = OrderSheet::where('status', 1)->where('drp_type', 4)->get();
        $settlementLetterTemplates = SettlementLetter::where('status', 1)->where('drp_type', 4)->get();

        //ZegoCloud Service---------------------
        $localUserID = $drp->slug; // Arbitrator
        $remoteUserID = $caseIds; // Individual
        $roomID = $room_id;
     
        $zegoToken = $this->generateZegoToken($localUserID, $drp->name);

        return view('drp.mediatormeetingroom.livemediatormeetingroom', compact('drp',
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

    public function saveRecording(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|string',
            'recording' => 'required|file|mimes:mp4,mkv,avi,flv,wmv',
        ]);

        // Locate the mediatormeetingroom based on room ID
        $mediatormeetingroom = MediatorMeetingRoom::where('room_id', $validated['room_id'])->first();

        if ($mediatormeetingroom) {
            // Save the uploaded recording using your existing helper function
            $recordingPath = Helper::saveFile($request->file('recording'), 'recordings');

            // Update the recording URL in the database
            $mediatormeetingroom->recording_url = $recordingPath;
            $mediatormeetingroom->save();

            return response()->json([
                'message' => 'Recording saved successfully.',
                'path' => $recordingPath,
            ], 200);
        } else {
            return response()->json(['message' => 'MeetingRoom not found.'], 404);
        }
    }
    
    public function getFlattenedCaseData($caseId)
    {
        $caseData = FileCase::select('file_cases.*', 'drps.name as mediator_name')
            ->with(['file_case_details', 'guarantors'])
            ->join('assign_cases', 'assign_cases.case_id', '=', 'file_cases.id')
            ->join('drps', 'drps.id', '=', 'assign_cases.mediator_id')
            ->where('file_cases.id', $caseId)
            ->first();
        
        if ($caseData) {
            $flattenedCaseData = $this->flattenCaseData(collect([$caseData]));
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
            ->whereIn('notice_type', [11,12,13,14,15,16,17,18,19,20,21,22,23])
            // ->where('email_status', 1)
            ->get();

        return response()->json($notices);
    }

    public function fetchSettlementAgreementsByCaseId(Request $request)
    {
        $caseId = $request->case_id;
        $notices = Notice::where('file_case_id', $caseId)
            ->whereIn('notice_type', [24,25])
            // ->where('email_status', 1)
            ->get();

        return response()->json($notices);
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
            // Set $noticeType based on $request->tempType
            switch ($request->tempType) {
                case 1: $noticeType = 9;
                    break;
                case 2: $noticeType = 10;
                    break;
                case 3: $noticeType = 11;
                    break;
                case 4: $noticeType = 12;
                    break;
                case 5: $noticeType = 13;
                    break;
                case 6: $noticeType = 14;
                    break;
                case 7: $noticeType = 15;
                    break;
                case 8: $noticeType = 16;
                    break;
                case 9: $noticeType = 17;
                    break;
                case 10: $noticeType = 18;
                    break;
                case 11: $noticeType = 19;
                    break;
                case 12: $noticeType = 20;
                    break;
                case 13: $noticeType = 21;
                    break;
                case 14: $noticeType = 22;
                    break;
                case 15: $noticeType = 23;
                    break;
                default: $noticeType = 0; // default case or fallback value
                    break;
            }

            $noticeexistData = Notice::where('file_case_id', $request->file_case_id)
                                    ->where('notice_type', $noticeType)->first();
           
            if (empty($noticeexistData)) {
                // Get signature settings
                $drp = auth('drp')->user();
                $signature = $drp->signature_drp;

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
                if (!empty($signature)) {
                    $html .= '
                        <div style="text-align: right; margin-top: 20px;">
                            <img src="' . asset('storage/' . $signature) . '" style="height: 80px;" alt="Signature">
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
                
                // Return JSON response instead of back()->withSuccess()
                return response()->json(['success' => true, 'message' => 'Notice/OrderSheet saved successfully.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Notice/OrderSheet already exists for this Case.']);
            } 
        }

        elseif($request->docType == 'settlementletter')
        {
            $noticeType = $request->tempType == 1 ? 24 : 25;

            $noticeexistData = Notice::where('file_case_id', $request->file_case_id)
                                    ->where('notice_type', $noticeType)->first();
            
            if (empty($noticeexistData)) {
                // Get signature settings
                $drp = auth('drp')->user();
                $signature = $drp->signature_drp;

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
                if (!empty($signature)) {
                    $html .= '
                        <div style="text-align: right; margin-top: 20px;">
                            <img src="' . asset('storage/' . $signature) . '" style="height: 80px;" alt="Signature">
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
                
                // Return JSON response instead of back()->withSuccess()
                return response()->json(['success' => true, 'message' => 'Notice/OrderSheet saved successfully.']);
            } else {
                return response()->json(['success' => false, 'message' => 'Notice/OrderSheet already exists for this Case.']);
            }
        }
    }

    public function closemediatormeetingroom(Request $request)
    {
        $roomId = $request->room_id;

        $mediatormeetingroom = MediatorMeetingRoom::where('room_id', $roomId)->first();

        if ($mediatormeetingroom) {
            $mediatormeetingroom->status = 0; // Change status to 0
            $mediatormeetingroom->save();

            return response()->json([
                'success' => true,
                'message' => 'Court Room closed successfully.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Court Room not found.'
        ]);
    }
}