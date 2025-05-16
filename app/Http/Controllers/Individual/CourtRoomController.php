<?php

namespace App\Http\Controllers\Individual;

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
        $this->middleware('auth:individual');
    }

    public function index(Request $request): View | JsonResponse
    {
        $title = 'Court Room List';
        $individual = auth('individual')->user();

        if (!$individual) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }

        $courtRoomLiveUpcoming = CourtRoom::select(
                'court_rooms.*',
                'drps.name as arbitrator_name',
                'file_cases.id as case_id',
                'file_cases.case_number'
            )
            ->leftJoin('drps', 'drps.id', '=', 'court_rooms.arbitrator_id')
            ->leftJoin('file_cases', function($join) use ($individual) {
                $join->on('file_cases.individual_id', '=', DB::raw($individual->id));
            })
            ->whereRaw("FIND_IN_SET(?, court_rooms.individual_id) > 0", [$individual->id])
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
            ->get();

        $courtRoomLiveClosed = CourtRoom::select(
                'court_rooms.*',
                'drps.name as arbitrator_name',
                'file_cases.id as case_id',
                'file_cases.case_number'
            )
            ->leftJoin('drps', 'drps.id', '=', 'court_rooms.arbitrator_id')
            ->leftJoin('file_cases', function($join) use ($individual) {
                $join->on('file_cases.individual_id', '=', DB::raw($individual->id));
            })
            ->whereRaw("FIND_IN_SET(?, court_rooms.individual_id) > 0", [$individual->id])
            ->where('court_rooms.status', 0) 
            ->where(function ($query) {
                $query->where('court_rooms.date', '<', Carbon::today()->toDateString())
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('court_rooms.date', Carbon::today()->toDateString())
                                ->where('court_rooms.time', '<=', Carbon::now()->format('H:i:s'));
                    });
            })
            ->get();

        $upcomingroomCount = $courtRoomLiveUpcoming->count();
        $closedroomCount = $courtRoomLiveClosed->count();

        $upcomingRooms = $courtRoomLiveUpcoming;
        $closedRooms = $courtRoomLiveClosed;

        return view('individual.courtroom.courtroomlist', compact('individual','title','upcomingRooms','closedRooms','upcomingroomCount','closedroomCount'));
    }


    public function livecourtroom(Request $request, $room_id): View | JsonResponse | RedirectResponse
    {
        $title = 'Live Court Room';
        $individual = auth('individual')->user();

        if (!$individual) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }

        $caseId = $request->query('case_id');
       
        $caseData = FileCase::select('file_cases.*', 'drps.id as arbitrator_id', 'drps.name as arbitrator_name')
            ->with(['file_case_details', 'guarantors'])
            ->join('assign_cases', 'assign_cases.case_id', '=', 'file_cases.id')
            ->join('drps', 'drps.id', '=', 'assign_cases.arbitrator_id')
            ->where('file_cases.id', $caseId) // Use whereIn instead of find()
            ->get();

        $noticeData = Notice::where('file_case_id', $caseId)
            ->whereIn('notice_type', [1,2,3,4,5,6,7,8,9,10])
            // ->where('email_status', 1)
            ->get();
    
        //ZegoCloud Service---------------------
        $localUserID = $individual->slug; // e.g., Individual 
        $remoteUserID = $caseData->first()->arbitrator_id; // Drp
        $roomID = $room_id;
    
        $zegoToken = $this->generateZegoToken($localUserID, $individual->name);
        
        return view('individual.courtroom.livecourtroom', compact(
            'individual',
            'title',
            'caseData',
            'noticeData',
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

    
}