<?php

namespace App\Http\Controllers\Individual;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\CourtRoom;
use App\Models\FileCase;
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

        $courtRoomLiveUpcoming = CourtRoom::select('court_rooms.*', 'drps.name as arbitrator_name')
            ->leftJoin('drps', 'drps.id', '=', 'court_rooms.arbitrator_id')
            ->where('court_rooms.individual_id', $individual->id)
            ->where(function ($query) {
                $query->where('court_rooms.date', '>', Carbon::today()->toDateString())
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('court_rooms.date', Carbon::today()->toDateString())
                                ->where('court_rooms.time', '>=', Carbon::now()->format('H:i:s'));
                    });
            })
            ->get();

        $courtRoomLiveClosed = CourtRoom::select('court_rooms.*', 'drps.name as arbitrator_name')
            ->leftJoin('drps', 'drps.id', '=', 'court_rooms.arbitrator_id')
            ->where('court_rooms.individual_id', $individual->id)
            ->where('court_rooms.status', 0) 
            ->where(function ($query) {
                $query->where('court_rooms.date', '<', Carbon::today()->toDateString())
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('court_rooms.date', Carbon::today()->toDateString())
                                ->where('court_rooms.time', '<', Carbon::now()->format('H:i:s'));
                    });
            })
            ->get();

        $upcomingRooms = $courtRoomLiveUpcoming;
        $closedRooms = $courtRoomLiveClosed;

        return view('individual.courtroom.courtroomlist', compact('individual','title','upcomingRooms','closedRooms'));
    }


    public function livecourtroom(Request $request, $caseID): View | JsonResponse | RedirectResponse
    {
        $title = 'Live Court Room';
        $individual = auth('individual')->user();

        if (!$individual) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }

        $caseData = FileCase::select('file_cases.*','drps.name as arbitrator_name')->with(['file_case_details', 'guarantors'])
            ->join('assign_cases','assign_cases.case_id','=','file_cases.id')
            ->join('drps','drps.id','=','assign_cases.arbitrator_id')
            ->find($caseID);

        //ZegoCloud Service---------------------
        $localUserID = $individual->id; // e.g., Individual 
        $remoteUserID = $caseData->id; // Individual or Organization
        $roomID = $caseData->case_number;

        $zegoToken = $this->generateZegoToken($localUserID, $individual->name);
        
        return view('individual.courtroom.livecourtroom', compact(
            'individual',
            'title',
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