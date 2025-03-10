<?php

namespace App\Http\Controllers\Drp;

use App\Http\Controllers\Controller;
use App\Models\Drp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:drp');
    }

    public function index(Request $request): View | RedirectResponse
    {
        $title = 'Dashboard';
        $drp = auth('drp')->user();

        $drpId = $drp ? $drp->drpId : null;
        $drpSlug = $drp ? $drp->slug : null;

        $drps = Drp::where('slug', $drpSlug)->get();
        if ($drps->count()) {
            return view('drp.dashboard', compact('drps','title'));
        } else {
            return to_route('home')->withInfo('Please enter your valid details.');
        }
    }

}
