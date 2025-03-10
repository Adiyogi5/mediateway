<?php

namespace App\Http\Controllers\Individual;

use App\Http\Controllers\Controller;
use App\Models\Individual;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:individual');
    }

    public function index(Request $request): View | RedirectResponse
    {
        $title = 'Individual Dashboard';

        $individual = auth('individual')->user();

        $individualId = $individual ? $individual->individualId : null;
        $individualSlug = $individual ? $individual->slug : null;

        $indivuduals = Individual::where('slug', $individualSlug)->get();
        if ($indivuduals->count()) {
            return view('individual.dashboard', compact('indivuduals','title'));
        } else {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }
    }

}
