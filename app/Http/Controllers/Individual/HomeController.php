<?php

namespace App\Http\Controllers\Individual;

use App\Http\Controllers\Controller;
use App\Models\FileCase;
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
        $individualSlug = $individual?->slug;
    
        $indivuduals = Individual::with('individualDetail')->where('slug', $individualSlug)->first();
        $caseData = FileCase::with([
                'payments',
                'assignedCases.arbitrator',
                'assignedCases.advocate',
                'assignedCases.caseManager',
                'assignedCases.mediator',
                'assignedCases.conciliator'
            ])
            ->leftJoin('states as claimant_states', 'claimant_states.id', '=', 'file_cases.claimant_state_id')
            ->leftJoin('cities as claimant_cities', 'claimant_cities.id', '=', 'file_cases.claimant_city_id')
            ->leftJoin('states as respondent_states', 'respondent_states.id', '=', 'file_cases.respondent_state_id')
            ->leftJoin('cities as respondent_cities', 'respondent_cities.id', '=', 'file_cases.respondent_city_id')
            ->where('file_cases.individual_id', $indivuduals->id)
            ->where('file_cases.status', 1)
            ->select([
                'file_cases.*',
                'claimant_states.name as claimant_state_name',
                'claimant_cities.name as claimant_city_name',
                'respondent_states.name as respondent_state_name',
                'respondent_cities.name as respondent_city_name',
            ])
            ->latest()
            ->get();    

        if (!$indivuduals) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }
        
        // Required fields to check
        $requiredFields = [
            'name', 'email', 'mobile', 'state_id', 'city_id', 'image', 
            'last_name', 'dob', 'nationality', 'gender', 'pincode', 
            'father_name', 'address1'
        ];

        $requiredDetailFields = ['university', 'degree', 'year'];

        // Check if any field is null or empty
        $missingFields = collect($requiredFields)->filter(fn($field) => empty($indivuduals->$field));
        $missingDetailFields = collect($requiredDetailFields)->filter(fn($field) => empty($indivuduals->individualDetail?->$field));

        if ($missingFields->isNotEmpty() || $missingDetailFields->isNotEmpty()) {
            return view('individual.dashboard', compact('indivuduals', 'title'))
                ->with('showProfilePopup', true);
        }

        return view('individual.dashboard', compact('indivuduals', 'title', 'caseData'));
    }

}
