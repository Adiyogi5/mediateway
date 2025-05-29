<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Models\FileCase;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

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

        if ($organization) {
            // ✅ Get accessible organization IDs
            if ($organization->parent_id == 0) {
                $organizationIds = Organization::where('parent_id', $organization->id)->pluck('id')->toArray();
                $organizationIds[] = $organization->id; // Include parent
            } else {
                $organizationIds = [$organization->id]; // Only self
            }

            $totalFiledCases = FileCase::with('assignedCases')
                ->whereIn('file_cases.organization_id', $organizationIds)
                ->where('status', 1)
                ->count();

            $totalPendingCases = FileCase::with('assignedCases', 'notices')
                ->whereHas('notices', function ($query) {
                    $query->whereIn('notice_type', range(1, 19));
                })
                ->whereIn('organization_id', $organizationIds)
                ->where('status', 1)
                ->count();

            $totalNewCases = FileCase::with('assignedCases', 'notices')
                ->whereIn('organization_id', $organizationIds)
                ->where('status', 1)
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count();

            $awards = FileCase::with('assignedCases', 'notices')
                ->whereHas('notices', function ($query) {
                    $query->where('notice_type', 11);
                })
                ->whereIn('organization_id', $organizationIds)
                ->where('status', 1)
                ->count();

            $interimOrders = FileCase::with('assignedCases', 'notices')
                ->whereHas('notices', function ($query) {
                    $query->where('notice_type', 10);
                })
                ->whereIn('organization_id', $organizationIds)
                ->where('status', 1)
                ->count();

            $upcomingHearings = FileCase::with('assignedCases')
                ->where(function ($query) {
                    $query->whereDate('first_hearing_date', '>', now())
                        ->orWhereDate('second_hearing_date', '>', now())
                        ->orWhereDate('final_hearing_date', '>', now());
                })
                ->whereIn('organization_id', $organizationIds)
                ->where('status', 1)
                ->count();

            $arbitratorData = FileCase::leftJoin('assign_cases', 'file_cases.id', '=', 'assign_cases.case_id')
                ->leftJoin('organizations', 'organizations.id', '=', 'file_cases.organization_id')
                ->leftJoin('drps', 'drps.id', '=', 'assign_cases.arbitrator_id')
                ->whereIn('organizations.id', $organizationIds)
                ->where('file_cases.status', 1)
                ->select(
                    'file_cases.*',
                    'assign_cases.arbitrator_id',
                    'drps.name as arbitrator_name',
                    'drps.last_name as arbitrator_last_name',
                    'drps.email as arbitrator_email',
                    'drps.mobile as arbitrator_mobile',
                    'drps.gender as arbitrator_gender',
                    'drps.profession as arbitrator_profession',
                    'drps.specialization as arbitrator_specialization'
                )
                ->get()
                ->groupBy('arbitrator_id')
                ->toArray();

            $caseManagerData = FileCase::leftJoin('assign_cases', 'file_cases.id', '=', 'assign_cases.case_id')
                ->leftJoin('organizations', 'organizations.id', '=', 'file_cases.organization_id')
                ->leftJoin('drps', 'drps.id', '=', 'assign_cases.case_manager_id')
                ->whereIn('organizations.id', $organizationIds)
                ->where('file_cases.status', 1)
                ->select(
                    'file_cases.*',
                    'assign_cases.case_manager_id',
                    'drps.name as case_manager_name',
                    'drps.last_name as case_manager_last_name',
                    'drps.email as case_manager_email',
                    'drps.mobile as case_manager_mobile',
                    'drps.gender as case_manager_gender',
                    'drps.profession as case_manager_profession',
                    'drps.specialization as case_manager_specialization'
                )
                ->get()
                ->groupBy('case_manager_id')
                ->toArray();

            $childOrganizations = Organization::whereIn('id', $organizationIds)->get();
        } else {
            $totalFiledCases = $totalPendingCases = $totalNewCases = $upcomingHearings = $interimOrders = $awards = 0;
            $arbitratorData = $caseManagerData = null;
            $organizationIds = [];
            $childOrganizations = collect();
        }

        $organizationSlug = $organization ? $organization->slug : null;

        $organizations = Organization::where('slug', $organizationSlug)->get();

        if ($organizations->count()) {
            return view('organization.dashboard', compact(
                'organizations',
                'title',
                'totalFiledCases',
                'totalPendingCases',
                'totalNewCases',
                'upcomingHearings',
                'interimOrders',
                'awards',
                'arbitratorData',
                'caseManagerData',
                'childOrganizations'
            ));
        } else {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }
    }

    public function filter(Request $request)
    {
        $filters = [
            'product' => $request->product,
            'product_type' => $request->product_type,
        ];

        $organization = auth('organization')->user();

        if (!$organization) {
            return response()->json(['html' => '<p>Unauthorized</p>'], 401);
        }

        // Get child + self organization IDs
        if ($organization->parent_id == 0) {
            $organizationIds = Organization::where('parent_id', $organization->id)->pluck('id')->toArray();
            $organizationIds[] = $organization->id;
        } else {
            $organizationIds = [$organization->id];
        }

        $baseQuery = FileCase::with('assignedCases', 'notices')
            ->filter($filters)
            ->whereIn('organization_id', $organizationIds)
            ->where('status', 1);

        $totalFiledCases = (clone $baseQuery)->count();

        $totalPendingCases = (clone $baseQuery)
            ->whereHas('notices', function ($query) {
                $query->whereIn('notice_type', range(1, 19));
            })
            ->count();

        $totalNewCases = (clone $baseQuery)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $awards = (clone $baseQuery)
            ->whereHas('notices', function ($query) {
                $query->where('notice_type', 11);
            })
            ->count();

        $interimOrders = (clone $baseQuery)
            ->whereHas('notices', function ($query) {
                $query->where('notice_type', 10);
            })
            ->count();

        $upcomingHearings = (clone $baseQuery)
            ->where(function ($query) {
                $query->whereDate('first_hearing_date', '>', now())
                    ->orWhereDate('second_hearing_date', '>', now())
                    ->orWhereDate('final_hearing_date', '>', now());
            })
            ->count();

        $view = view('organization.partials.case-type-overview', compact(
            'totalFiledCases',
            'totalPendingCases',
            'totalNewCases',
            'awards',
            'interimOrders',
            'upcomingHearings'
        ))->render();

        return response()->json(['html' => $view]);
    }


}
