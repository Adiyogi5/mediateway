<?php

namespace App\Http\Controllers\Drp;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\OrderSheet;
use App\Models\Setting;
use App\Models\SettlementLetter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

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


    public function livecourtroom(Request $request): View | JsonResponse | RedirectResponse
    {
        $title = 'Live Court Room';
        $drp = auth('drp')->user();

        if (!$drp) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }
        if ($drp->drp_type !== 1) {
            return redirect()->route('drp.dashboard')->withError('Unauthorized access.');
        }

        $orderSheetTemplates = OrderSheet::where('status', 1)->where('drp_type', 5)->get();
        $settlementLetterTemplates = SettlementLetter::where('status', 1)->where('drp_type', 5)->get();

        return view('drp.courtroom.livecourtroom', compact('drp','title','orderSheetTemplates', 'settlementLetterTemplates'));
    }

    
}