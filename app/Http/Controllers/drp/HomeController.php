<?php

namespace App\Http\Controllers\Drp;

use App\Http\Controllers\Controller;
use App\Models\Drp;
use App\Models\FileCase;
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

        //########### Arbitrator ##############
        if ($drp->drp_type == 1) {
            $totalFiledCases = FileCase::with('assignedCases')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                    $query->where('arbitrator_id', $drp->id);
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $totalPendingCases = FileCase::with('assignedCases','notices')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                        $query->where('arbitrator_id', $drp->id);
                })
                ->whereHas('notices', function ($query) use ($drp) {
                    $query->whereIn('notice_type', [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19]);
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $totalResolvedCases = FileCase::with('assignedCases','notices')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                        $query->where('arbitrator_id', $drp->id);
                })
                ->whereHas('notices', function ($query) use ($drp) {
                    $query->where('notice_type', 20);
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $upcomingHearings = FileCase::with('assignedCases')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                    $query->where('arbitrator_id', $drp->id);
                })
                ->where(function ($query) {
                    $query->whereDate('first_hearing_date', '>', now())
                        ->orWhereDate('second_hearing_date', '>', now())
                        ->orWhereDate('final_hearing_date', '>', now());
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $interimOrders = FileCase::with('assignedCases','notices')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                        $query->where('arbitrator_id', $drp->id);
                })
                ->whereHas('notices', function ($query) use ($drp) {
                    $query->where('notice_type', 10);
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $awards = FileCase::with('assignedCases','notices')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                        $query->where('arbitrator_id', $drp->id);
                })
                ->whereHas('notices', function ($query) use ($drp) {
                    $query->where('notice_type', 11);
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $caseManagerData = FileCase::leftJoin('assign_cases', 'file_cases.id', '=', 'assign_cases.case_id')
                ->leftJoin('drps', 'drps.id', '=', 'assign_cases.case_manager_id')
                ->where('assign_cases.arbitrator_id', $drp->id)
                ->where('file_cases.status', 1)
                ->select('file_cases.*', 'assign_cases.case_manager_id', 'drps.name as case_manager_name', 'drps.last_name as case_manager_last_name', 'drps.email as case_manager_email', 'drps.mobile as case_manager_mobile', 'drps.gender as case_manager_gender', 'drps.profession as case_manager_profession', 'drps.specialization as case_manager_specialization')
                ->get()->groupBy('case_manager_id')->toArray();
        } 
        //########### Advocate ##############
        elseif ($drp->drp_type == 2) {
            $totalFiledCases = FileCase::with('assignedCases')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                    $query->where('advocate_id', $drp->id);
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $totalPendingCases = FileCase::with('assignedCases','notices')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                        $query->where('advocate_id', $drp->id);
                })
                ->whereHas('notices', function ($query) use ($drp) {
                    $query->whereIn('notice_type', [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19]);
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $totalResolvedCases = FileCase::with('assignedCases','notices')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                        $query->where('advocate_id', $drp->id);
                })
                ->whereHas('notices', function ($query) use ($drp) {
                    $query->where('notice_type', 20);
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $upcomingHearings = FileCase::with('assignedCases')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                    $query->where('advocate_id', $drp->id);
                })
                ->where(function ($query) {
                    $query->whereDate('first_hearing_date', '>', now())
                        ->orWhereDate('second_hearing_date', '>', now())
                        ->orWhereDate('final_hearing_date', '>', now());
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $interimOrders = FileCase::with('assignedCases','notices')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                        $query->where('advocate_id', $drp->id);
                })
                ->whereHas('notices', function ($query) use ($drp) {
                    $query->where('notice_type', 10);
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $awards = FileCase::with('assignedCases','notices')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                        $query->where('advocate_id', $drp->id);
                })
                ->whereHas('notices', function ($query) use ($drp) {
                    $query->where('notice_type', 11);
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

          $caseManagerData = FileCase::leftJoin('assign_cases', 'file_cases.id', '=', 'assign_cases.case_id')
                ->leftJoin('drps', 'drps.id', '=', 'assign_cases.case_manager_id')
                ->where('assign_cases.advocate_id', $drp->id)
                ->where('file_cases.status', 1)
                ->select('file_cases.*', 'assign_cases.case_manager_id', 'drps.name as case_manager_name', 'drps.last_name as case_manager_last_name', 'drps.email as case_manager_email', 'drps.mobile as case_manager_mobile', 'drps.gender as case_manager_gender', 'drps.profession as case_manager_profession', 'drps.specialization as case_manager_specialization')
                ->get()->groupBy('case_manager_id')->toArray();
        }
        //########### Case Manager ##############
        elseif ($drp->drp_type == 3) {
            $totalFiledCases = FileCase::with('assignedCases')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                    $query->where('case_manager_id', $drp->id);
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $totalPendingCases = FileCase::with('assignedCases','notices')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                        $query->where('case_manager_id', $drp->id);
                })
                ->whereHas('notices', function ($query) use ($drp) {
                    $query->whereIn('notice_type', [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19]);
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $totalResolvedCases = FileCase::with('assignedCases','notices')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                        $query->where('case_manager_id', $drp->id);
                })
                ->whereHas('notices', function ($query) use ($drp) {
                    $query->where('notice_type', 20);
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $upcomingHearings = FileCase::with('assignedCases')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                    $query->where('case_manager_id', $drp->id);
                })
                ->where(function ($query) {
                    $query->whereDate('first_hearing_date', '>', now())
                        ->orWhereDate('second_hearing_date', '>', now())
                        ->orWhereDate('final_hearing_date', '>', now());
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $interimOrders = FileCase::with('assignedCases','notices')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                        $query->where('case_manager_id', $drp->id);
                })
                ->whereHas('notices', function ($query) use ($drp) {
                    $query->where('notice_type', 10);
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $awards = FileCase::with('assignedCases','notices')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                        $query->where('case_manager_id', $drp->id);
                })
                ->whereHas('notices', function ($query) use ($drp) {
                    $query->where('notice_type', 11);
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

          $caseManagerData = FileCase::leftJoin('assign_cases', 'file_cases.id', '=', 'assign_cases.case_id')
                ->leftJoin('drps', 'drps.id', '=', 'assign_cases.arbitrator_id')
                ->where('assign_cases.case_manager_id', $drp->id)
                ->where('file_cases.status', 1)
                ->select('file_cases.*', 'assign_cases.arbitrator_id', 'drps.name as arbitrator_name', 'drps.last_name as arbitrator_last_name', 'drps.email as arbitrator_email', 'drps.mobile as arbitrator_mobile', 'drps.gender as arbitrator_gender', 'drps.profession as arbitrator_profession', 'drps.specialization as arbitrator_specialization')
                ->get()->groupBy('arbitrator_id')->toArray();

        } 
        //########### Mediator ##############
        elseif ($drp->drp_type == 4) {
            $totalFiledCases = FileCase::with('assignedCases')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                    $query->where('mediator_id', $drp->id);
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $totalPendingCases = FileCase::with('assignedCases','notices')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                        $query->where('mediator_id', $drp->id);
                })
                ->whereHas('notices', function ($query) use ($drp) {
                    $query->whereIn('notice_type', [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19]);
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $totalResolvedCases = FileCase::with('assignedCases','notices')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                        $query->where('mediator_id', $drp->id);
                })
                ->whereHas('notices', function ($query) use ($drp) {
                    $query->where('notice_type', 20);
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $upcomingHearings = FileCase::with('assignedCases')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                    $query->where('mediator_id', $drp->id);
                })
                ->where(function ($query) {
                    $query->whereDate('first_hearing_date', '>', now())
                        ->orWhereDate('second_hearing_date', '>', now())
                        ->orWhereDate('final_hearing_date', '>', now());
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $interimOrders = FileCase::with('assignedCases','notices')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                        $query->where('mediator_id', $drp->id);
                })
                ->whereHas('notices', function ($query) use ($drp) {
                    $query->where('notice_type', 10);
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $awards = FileCase::with('assignedCases','notices')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                        $query->where('mediator_id', $drp->id);
                })
                ->whereHas('notices', function ($query) use ($drp) {
                    $query->where('notice_type', 11);
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

          $caseManagerData = FileCase::leftJoin('assign_cases', 'file_cases.id', '=', 'assign_cases.case_id')
                ->leftJoin('drps', 'drps.id', '=', 'assign_cases.case_manager_id')
                ->where('assign_cases.mediator_id', $drp->id)
                ->where('file_cases.status', 1)
                ->select('file_cases.*', 'assign_cases.case_manager_id', 'drps.name as case_manager_name', 'drps.last_name as case_manager_last_name', 'drps.email as case_manager_email', 'drps.mobile as case_manager_mobile', 'drps.gender as case_manager_gender', 'drps.profession as case_manager_profession', 'drps.specialization as case_manager_specialization')
                ->get()->groupBy('case_manager_id')->toArray();
        }
        //########### Conciliator ##############
        elseif ($drp->drp_type == 5) {
            $totalFiledCases = FileCase::with('assignedCases')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                    $query->where('conciliator_id', $drp->id);
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $totalPendingCases = FileCase::with('assignedCases','notices')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                        $query->where('conciliator_id', $drp->id);
                })
                ->whereHas('notices', function ($query) use ($drp) {
                    $query->whereIn('notice_type', [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19]);
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $totalResolvedCases = FileCase::with('assignedCases','notices')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                        $query->where('conciliator_id', $drp->id);
                })
                ->whereHas('notices', function ($query) use ($drp) {
                    $query->where('notice_type', 20);
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $upcomingHearings = FileCase::with('assignedCases')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                    $query->where('conciliator_id', $drp->id);
                })
                ->where(function ($query) {
                    $query->whereDate('first_hearing_date', '>', now())
                        ->orWhereDate('second_hearing_date', '>', now())
                        ->orWhereDate('final_hearing_date', '>', now());
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $interimOrders = FileCase::with('assignedCases','notices')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                        $query->where('conciliator_id', $drp->id);
                })
                ->whereHas('notices', function ($query) use ($drp) {
                    $query->where('notice_type', 10);
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $awards = FileCase::with('assignedCases','notices')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                        $query->where('conciliator_id', $drp->id);
                })
                ->whereHas('notices', function ($query) use ($drp) {
                    $query->where('notice_type', 11);
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

            $settlementAgreements = FileCase::with('assignedCases','notices')
                ->whereHas('assignedCases', function ($query) use ($drp) {
                        $query->where('conciliator_id', $drp->id);
                })
                ->whereHas('notices', function ($query) use ($drp) {
                    $query->where('notice_type', 11);
                })
                ->where(function ($query) {
                    $query->where('status', 1);
                })
                ->count();

          $caseManagerData = FileCase::leftJoin('assign_cases', 'file_cases.id', '=', 'assign_cases.case_id')
                ->leftJoin('drps', 'drps.id', '=', 'assign_cases.case_manager_id')
                ->where('assign_cases.conciliator_id', $drp->id)
                ->where('file_cases.status', 1)
                ->select('file_cases.*', 'assign_cases.case_manager_id', 'drps.name as case_manager_name', 'drps.last_name as case_manager_last_name', 'drps.email as case_manager_email', 'drps.mobile as case_manager_mobile', 'drps.gender as case_manager_gender', 'drps.profession as case_manager_profession', 'drps.specialization as case_manager_specialization')
                ->get()->groupBy('case_manager_id')->toArray();
        }
        //########### Other ##############
        else{
            $totalFiledCases = $totalPendingCases = $totalResolvedCases = $upcomingHearings = $interimOrders = $awards = $settlementAgreements = 0;
            $caseManagerData = NULL;
        }

        //Auth DRP
        $drpId = $drp ? $drp->drpId : null;
        $drpSlug = $drp ? $drp->slug : null;

        $drps = Drp::where('slug', $drpSlug)->get();
         if ($drps->count()) {
            return view('drp.dashboard', compact(
                'drps',
                'title',
                'totalFiledCases',
                'totalPendingCases',
                'totalResolvedCases',
                'upcomingHearings',
                'interimOrders',
                'awards',
                'settlementAgreements',
                'caseManagerData'
            ));
        } else {
            return to_route('home')->withInfo('Please enter your valid details.');
        }
    }

}
