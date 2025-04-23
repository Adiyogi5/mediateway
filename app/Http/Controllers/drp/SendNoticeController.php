<?php

namespace App\Http\Controllers\Drp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SendNoticeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:drp');
    }

    public function noticelist(Request $request): View
    {
        $title = 'Send Notice List';

        // Ensure the user is authenticated and has drp_type == 1
        if (!auth('drp')->check() || auth('drp')->user()->drp_type != 3) {
            return redirect()->route('drp.dashboard')->with('error', 'UnAuthentication Access..!!');
        }

        return view('drp.notices.noticelist', compact('title'));
    }


}