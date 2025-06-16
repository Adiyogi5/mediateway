<?php

namespace App\Http\Controllers;

use App\Models\SmsCount;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        return view('home');
    }

    public function all_sms_count(Request $request): View | RedirectResponse
    {

        $total_sms_count = SmsCount::orderBy('created_at', 'desc')->get();

        if ($total_sms_count->count()) {
            return view('all_sms_count', compact('total_sms_count'));
        } else {
            return to_route('home');
        }
    }
}
