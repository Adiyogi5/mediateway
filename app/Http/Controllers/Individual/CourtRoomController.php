<?php

namespace App\Http\Controllers\Individual;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Setting;
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
        $this->middleware('auth:individual');
    }

    public function index(Request $request): View | JsonResponse
    {
        $title = 'Court Room List';
        $individual = auth('individual')->user();

        if (!$individual) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }

        return view('individual.courtroom.courtroomlist', compact('individual','title'));
    }

    public function livecourtroom(Request $request): View | JsonResponse | RedirectResponse
    {
        $title = 'Live Court Room';
        $individual = auth('individual')->user();

        if (!$individual) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }
        
        return view('individual.courtroom.livecourtroom', compact('individual','title'));
    }
    
}