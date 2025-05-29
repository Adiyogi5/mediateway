<?php

namespace App\Http\Controllers\Drp;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\AssignCase;
use App\Models\CourtRoom;
use App\Models\FileCase;
use App\Models\Notice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;

class CaseListController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:drp');
    }

    public function index(Request $request): View | JsonResponse | RedirectResponse
    {
        $title = 'All Cases List';
        $drp = auth('drp')->user();

        if (!$drp) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }
        if ($drp->drp_type !== 1) {
            return redirect()->route('drp.dashboard')->withError('Unauthorized access.');
        }

        if ($request->ajax()) {
            $data = FileCase::select('file_cases.id', 'file_cases.case_type', 'file_cases.product_type', 'file_cases.case_number', 'file_cases.loan_number', 'file_cases.status', 'file_cases.created_at','assign_cases.arbitrator_id','assign_cases.confirm_to_arbitrator')
                ->join('assign_cases','assign_cases.case_id','=','file_cases.id')
                ->where('assign_cases.arbitrator_id',$drp->id)
                ->where('file_cases.status', 1);
                
                if ($request->filled('case_type')) {
                    $data->where('file_cases.case_type', $request->case_type);
                }

                if ($request->filled('product_type')) {
                    $data->where('file_cases.product_type', $request->product_type);
                }

                if ($request->filled('case_number')) {
                    $data->where('file_cases.case_number', 'like', '%' . $request->case_number . '%');
                }

                if ($request->filled('loan_number')) {
                    $data->where('file_cases.loan_number', 'like', '%' . $request->loan_number . '%');
                }

                if ($request->filled('arbitrator_status')) {
                    $data->where('assign_cases.confirm_to_arbitrator', $request->arbitrator_status);
                }

                if ($request->filled('status')) {
                    $data->where('file_cases.status', $request->status);
                }

                if ($request->filled('date_from') && $request->filled('date_to')) {
                    $data->whereBetween('file_cases.created_at', [
                        $request->date_from . ' 00:00:00',
                        $request->date_to . ' 23:59:59'
                    ]);
                }
            return Datatables::of($data)
                ->editColumn('case_type', function ($row) {
                    return config('constant.case_type')[$row->case_type] ?? 'Unknown';
                })
                ->editColumn('product_type', function ($row) {
                    return config('constant.product_type')[$row->product_type] ?? 'Unknown';
                })
                ->editColumn('created_at', function ($row) {
                    return $row['created_at']->format('d M, Y');
                })  
                ->addColumn('arbitrator_status', function ($row) {
                    return $row['confirm_to_arbitrator'] == 0
                        ? '<small class="badge fw-semi-bold rounded-pill badge-danger">Pending</small>'
                        : '<small class="badge fw-semi-bold rounded-pill badge-success">Approved</small>';
                })  
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';

                   // Check if a valid notice exists
                    $hasNotice = Notice::where('file_case_id', $row->id)
                        ->where('notice_type', 7)//Notice 3C send for arbitrator accept the case then show approve button
                        // ->where('email_status', 1)
                        ->exists();
                    // Check if case is already approved
                    $isApproved = AssignCase::where('case_id', $row->id)
                        ->where('arbitrator_id', auth('drp')->id())
                        ->where('confirm_to_arbitrator', 1)
                        ->exists();
                    if ($hasNotice && !$isApproved) {
                        $btn .= '<a class="dropdown-item btn-approve-case" href="javascript:void(0)" data-id="' . $row->id . '">Approve Case</a>';
                    }

                    $btn .= '<a class="dropdown-item" href="' . route('drp.allcases.viewcasedetail', $row->id) . '">View Case Details</a>';
                    $btn .= '</div>'; 
                    return $btn;
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'status', 'case_type', 'product_type', 'arbitrator_status'])
                ->make(true);
        }
        return view('drp.allcases.caselist', compact('drp','title'));
    }


    public function approveCase(Request $request): JsonResponse
    {
        $request->validate([
            'case_id' => 'required|exists:assign_cases,case_id'
        ]);

        $assign = AssignCase::where('case_id', $request->case_id)
            ->where('arbitrator_id', auth('drp')->id())
            ->first();

        if (!$assign) {
            return response()->json(['success' => false, 'message' => 'Unauthorized or case not found.']);
        }

        $assign->confirm_to_arbitrator = 1;
        $assign->save();

        return response()->json(['success' => true, 'message' => 'Case approved successfully.']);
    }


     public function viewcasedetail($id): View|RedirectResponse
    {
        $title = 'Filed Case Detail';
        
        $caseviewData = FileCase::find($id);
        
        if (!$caseviewData) {
            return to_route('cases.filecaseview')->with('error', 'Filed Case Not Found..!!');
        }

        $caseData = FileCase::with([
                'file_case_details', 
                'guarantors',
                'notices', 
                'assignedCases.arbitrator', 
                'assignedCases.advocate', 
                'assignedCases.caseManager', 
                'assignedCases.mediator', 
                'assignedCases.conciliator'
            ])
            ->where('id', $caseviewData->id)
            ->where('status', 1)
            ->latest()
            ->get();

        $upcomingHearings = CourtRoom::where('court_room_case_id', $caseviewData->id)
            ->where(function ($query) {
                $query->where('date', '>', Carbon::today()->toDateString())
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('date', Carbon::today()->toDateString())
                                ->where('time', '>=', Carbon::now()->format('H:i:s'));
                    })
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('date', Carbon::today()->toDateString())
                                ->where('status', 1);
                    });
            })->get();

        $closedHearings = CourtRoom::where('court_room_case_id', $caseviewData->id)
            ->where(function ($query) {
                $query->where('date', '<', Carbon::today()->toDateString())
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('date', Carbon::today()->toDateString())
                                ->where('time', '<=', Carbon::now()->format('H:i:s'));
                    });
            })->get();

        if ($caseData->isEmpty()) {
            return to_route('allcases.caselist')->with('error', 'You are not authorized to view this case.');
        }
    
        return view('drp.allcases.viewcasedetail', compact('caseviewData', 'title','caseData','upcomingHearings','closedHearings'));
    }
}