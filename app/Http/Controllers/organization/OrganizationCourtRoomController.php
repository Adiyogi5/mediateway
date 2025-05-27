<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\CourtRoom;
use App\Models\FileCase;
use App\Models\Notice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrganizationCourtRoomController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:organization');
    }

    public function index(Request $request): View | JsonResponse
    {
        $title = 'Court Room List';
        $organization = auth('organization')->user();

        if (!$organization) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }

        // Get the parent organization ID if it exists, otherwise use its own ID
        $parentId = $organization->parent_id ?? $organization->id;

        // ---------------------- UPCOMING COURT ROOMS ----------------------
        $courtRoomLiveUpcoming = CourtRoom::select(
                'court_rooms.*',
                'drps.name as arbitrator_name',
                DB::raw('GROUP_CONCAT(DISTINCT file_cases.case_number SEPARATOR ", ") as case_numbers')
            )
            ->leftJoin('drps', 'drps.id', '=', 'court_rooms.arbitrator_id')
            ->leftJoin('file_cases', function ($join) {
                $join->on(DB::raw('FIND_IN_SET(file_cases.id, court_rooms.court_room_case_id)'), '>', DB::raw('0'));
            })
            ->where(function ($query) use ($organization, $parentId) {
                $query->whereRaw("FIND_IN_SET(?, court_rooms.organization_id) > 0", [$parentId])
                    ->orWhereRaw("FIND_IN_SET(?, court_rooms.organization_id) > 0", [$organization->id]);
            })
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

        // ---------------------- CLOSED COURT ROOMS ----------------------
        $courtRoomLiveClosed = CourtRoom::select(
                'court_rooms.*',
                'drps.name as arbitrator_name',
                DB::raw('GROUP_CONCAT(DISTINCT file_cases.case_number SEPARATOR ", ") as case_numbers')
            )
            ->leftJoin('drps', 'drps.id', '=', 'court_rooms.arbitrator_id')
            ->leftJoin('file_cases', function ($join) {
                $join->on(DB::raw('FIND_IN_SET(file_cases.id, court_rooms.court_room_case_id)'), '>', DB::raw('0'));
            })
            ->where(function ($query) use ($organization, $parentId) {
                $query->whereRaw("FIND_IN_SET(?, court_rooms.organization_id) > 0", [$parentId])
                    ->orWhereRaw("FIND_IN_SET(?, court_rooms.organization_id) > 0", [$organization->id]);
            })
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

        $upcomingroomCount = $courtRoomLiveUpcoming->count();
        $closedroomCount = $courtRoomLiveClosed->count();

        $upcomingRooms = $courtRoomLiveUpcoming;
        $closedRooms = $courtRoomLiveClosed;

        return view('organization.organizationcourtroom.organizationcourtroomlist', compact('organization', 'title', 'upcomingRooms', 'closedRooms','upcomingroomCount','closedroomCount'));
    }

    public function upcomingRoomsData()
    {
        $organization = auth('organization')->user();

        // Get the parent organization ID if it exists, otherwise use its own ID
        $parentId = $organization->parent_id ?? $organization->id;

        // ---------------------- UPCOMING COURT ROOMS ----------------------
        $upcomingRooms = CourtRoom::select(
                'court_rooms.*',
                'drps.name as arbitrator_name',
                DB::raw('GROUP_CONCAT(DISTINCT file_cases.case_number SEPARATOR ", ") as case_numbers')
            )
            ->leftJoin('drps', 'drps.id', '=', 'court_rooms.arbitrator_id')
            ->leftJoin('file_cases', function ($join) {
                $join->on(DB::raw('FIND_IN_SET(file_cases.id, court_rooms.court_room_case_id)'), '>', DB::raw('0'));
            })
            ->where(function ($query) use ($organization, $parentId) {
                $query->whereRaw("FIND_IN_SET(?, court_rooms.organization_id) > 0", [$parentId])
                    ->orWhereRaw("FIND_IN_SET(?, court_rooms.organization_id) > 0", [$organization->id]);
            })
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
            ->addColumn('arbitrator_name', function ($room) {
                $arbitrators = array_filter(array_map('trim', explode(',', $room->arbitrator_name)));

                $main = $arbitrators[0] ?? '-';

                if (count($arbitrators) > 1) {
                    $list = '<ul class="p-1 m-0">';
                    foreach ($arbitrators as $item) {
                        $list .= '<li>' . e($item) . '</li>';
                    }
                    $list .= '</ul>';

                    $main .= ' <i class="fa fa-info-circle text-primary ml-2 info-icon" data-bs-toggle="popover" data-bs-html="true" data-bs-content="' . e($list) . '"></i>';
                }

                return $main;
            })
            ->addColumn('hearing_type', function ($room) {
                return match($room->hearing_type) {
                    1 => 'First Hearing',
                    2 => 'Second Hearing',
                    3 => 'Final Hearing',
                    default => 'Unknown',
                };
            })
            ->addColumn('date', fn($room) => \Carbon\Carbon::parse($room->date)->format('d F Y'))
            ->addColumn('time', fn($room) => \Carbon\Carbon::parse($room->time)->format('h:i A'))
            ->addColumn('status', fn($room) => $room->status == 1 ? '<span class="fa fa-check pl-3"></span>' : '<span class="fa fa-clock pl-3"></span>')
            ->addColumn('action', function ($room) {
                if ($room->status == 1) {
                    return '<a href="' . route('organization.organizationcourtroom.liveorganizationcourtroom', $room->room_id) . '?case_id=' . $room->court_room_case_id . '" class="fa fa-video btn bg-success text-white fs-6"></a>';
                } else {
                    return '<span class="fa fa-video btn bg-secondary text-white fs-6" style="cursor:not-allowed;"></span>';
                }
            })
            ->rawColumns(['case_numbers', 'arbitrator_name', 'status', 'action'])
            ->make(true);
    }

    public function closedRoomsData()
    {
        $organization = auth('organization')->user();

        // Get the parent organization ID if it exists, otherwise use its own ID
        $parentId = $organization->parent_id ?? $organization->id;

        // ---------------------- CLOSED COURT ROOMS ----------------------
        $closedRooms = CourtRoom::select(
                'court_rooms.*',
                'drps.name as arbitrator_name',
                DB::raw('GROUP_CONCAT(DISTINCT file_cases.case_number SEPARATOR ", ") as case_numbers')
            )
            ->leftJoin('drps', 'drps.id', '=', 'court_rooms.arbitrator_id')
            ->leftJoin('file_cases', function ($join) {
                $join->on(DB::raw('FIND_IN_SET(file_cases.id, court_rooms.court_room_case_id)'), '>', DB::raw('0'));
            })
            ->where(function ($query) use ($organization, $parentId) {
                $query->whereRaw("FIND_IN_SET(?, court_rooms.organization_id) > 0", [$parentId])
                    ->orWhereRaw("FIND_IN_SET(?, court_rooms.organization_id) > 0", [$organization->id]);
            })
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

        return datatables()->of($closedRooms)
            ->addColumn('case_numbers', function ($room) {
                $main = Str::before($room->case_numbers, ',') ?? '-';
                if (Str::contains($room->case_numbers, ',')) {
                    $list = '<ul class="p-1">';
                    foreach (explode(',', $room->case_numbers) as $case) {
                        $list .= "<li>$case</li>";
                    }
                    $list .= '</ul>';
                    $main .= ' <i class="fa fa-info-circle text-primary ml-2 info-icon" data-bs-toggle="popover" data-bs-html="true" data-bs-content="' . e($list) . '"></i>';
                }
                return $main;
            })
            ->addColumn('arbitrator_name', function ($room) {
                $arbitrators = array_filter(array_map('trim', explode(',', $room->arbitrator_name)));

                $main = $arbitrators[0] ?? '-';

                if (count($arbitrators) > 1) {
                    $list = '<ul>';
                    foreach ($arbitrators as $item) {
                        $list .= '<li>' . e($item) . '</li>';
                    }
                    $list .= '</ul>';

                    $main .= ' <i class="fa fa-info-circle text-primary ml-2 info-icon" data-bs-toggle="popover" data-bs-html="true" data-bs-content="' . e($list) . '"></i>';
                }

                return $main;
            })
            ->addColumn('hearing_type', function ($room) {
                return match($room->hearing_type) {
                    1 => 'First Hearing',
                    2 => 'Second Hearing',
                    3 => 'Final Hearing',
                    default => 'Unknown',
                };
            })
            ->addColumn('date', fn($room) => \Carbon\Carbon::parse($room->date)->format('d F Y'))
            ->addColumn('time', fn($room) => \Carbon\Carbon::parse($room->time)->format('h:i A'))
            ->addColumn('status', fn($room) => $room->status == 1 ? '<span class="fa fa-check pl-3"></span>' : '<span class="fa fa-clock pl-3"></span>')
            ->addColumn('action', fn($room) => '<button class="fa fa-handshake btn bg-secondary text-white"></button>')
            ->rawColumns(['case_numbers', 'arbitrator_name', 'status', 'action'])
            ->make(true);
    }


    public function livecourtroom(Request $request, $room_id): View | JsonResponse | RedirectResponse
    {
        $title = 'Live Court Room';
        $organization = auth('organization')->user();

        if (!$organization) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }

        $caseIds = explode(',', $request->query('case_id'));
    
        $caseData = FileCase::select('file_cases.*', 'drps.id as arbitrator_id', 'drps.name as arbitrator_name')
            ->with(['file_case_details', 'guarantors'])
            ->join('assign_cases', 'assign_cases.case_id', '=', 'file_cases.id')
            ->join('drps', 'drps.id', '=', 'assign_cases.arbitrator_id')
            ->whereIn('file_cases.id', $caseIds)
            ->get();
   
        //ZegoCloud Service---------------------
        $localUserID = $organization->slug; // e.g., organization 
        $remoteUserID = $caseData->first()->arbitrator_id; // Drp
        $roomID = $room_id;
    
        $zegoToken = $this->generateZegoToken($localUserID, $organization->name);
        
        return view('organization.organizationcourtroom.liveorganizationcourtroom', compact(
            'organization',
            'title',
            'caseData',
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
    
}