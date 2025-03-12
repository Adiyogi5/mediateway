<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:organization');
    }

    public function index(Request $request): View | RedirectResponse
    {
        $title = 'Dashboard';
        $organization = auth('organization')->user();
        
        $organizationId = $organization ? $organization->organizationId : null;
        $organizationSlug = $organization ? $organization->slug : null;
        
        $organizations = Organization::where('slug', $organizationSlug)->get();
        
        if ($organizations->count()) {
            return view('organization.dashboard', compact('organizations','title'));
        } else {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }
    }

}
