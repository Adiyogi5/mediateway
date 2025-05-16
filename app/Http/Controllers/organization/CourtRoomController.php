<?php

namespace App\Http\Controllers\Organization;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\CourtRoom;
use App\Models\FileCase;
use App\Models\Notice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CourtRoomController extends Controller
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
            ->groupBy('court_rooms.id', 'court_rooms.court_room_case_id')
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
            ->groupBy('court_rooms.id', 'court_rooms.court_room_case_id')
            ->get();

        $upcomingroomCount = $courtRoomLiveUpcoming->count();
        $closedroomCount = $courtRoomLiveClosed->count();

        $upcomingRooms = $courtRoomLiveUpcoming;
        $closedRooms = $courtRoomLiveClosed;

        return view('organization.courtroom.courtroomlist', compact('organization', 'title', 'upcomingRooms', 'closedRooms','upcomingroomCount','closedroomCount'));
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
        
        return view('organization.courtroom.livecourtroom', compact(
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
            ->whereIn('notice_type', [11,12,13,14,15,16,17,18,19,20,21,22])
            // ->where('email_status', 1)
            ->get();

        return response()->json($notices);
    }
    
}