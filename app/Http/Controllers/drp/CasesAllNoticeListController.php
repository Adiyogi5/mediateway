<?php

namespace App\Http\Controllers\Drp;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Config;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CasesAllNoticeListController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:drp');
    }

    public function index(Request $request): View | JsonResponse | RedirectResponse
    {
        $title = 'All Case Notice List';
        $drp = auth('drp')->user();

        if (!$drp) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }
        if ($drp->drp_type !== 3) {
            return redirect()->route('drp.dashboard')->withError('Unauthorized access.');
        }
        if ($request->ajax()) {
            $latestNoticesQuery = DB::table('notices as n1')
            ->select('n1.file_case_id', 'n1.notice_type', 'n1.created_at', 'n1.notice')
            ->join(DB::raw('
                (SELECT file_case_id, notice_type, MAX(created_at) as max_created
                FROM notices
                GROUP BY file_case_id, notice_type) as n2
            '), function ($join) {
                $join->on('n1.file_case_id', '=', 'n2.file_case_id')
                     ->on('n1.notice_type', '=', 'n2.notice_type')
                     ->on('n1.created_at', '=', 'n2.max_created');
            });
        
        // Use this query as a subquery
        $data = DB::table('file_cases')
            ->select(
                'file_cases.id',
                'file_cases.case_type',
                'file_cases.case_number',
                'file_cases.loan_number',
                'file_cases.status',
                'file_cases.created_at',
                DB::raw("MAX(CASE WHEN n.notice_type = 1 THEN n.notice END) as notice_1"),
                DB::raw("MAX(CASE WHEN n.notice_type = 2 THEN n.notice END) as notice_1a"),
                DB::raw("MAX(CASE WHEN n.notice_type = 3 THEN n.notice END) as notice_1b"),
                DB::raw("MAX(CASE WHEN n.notice_type = 4 THEN n.notice END) as notice_2b"),
                DB::raw("MAX(CASE WHEN n.notice_type = 5 THEN n.notice END) as notice_3a"),
                DB::raw("MAX(CASE WHEN n.notice_type = 6 THEN n.notice END) as notice_3b"),
                DB::raw("MAX(CASE WHEN n.notice_type = 7 THEN n.notice END) as notice_3c"),
                DB::raw("MAX(CASE WHEN n.notice_type = 8 THEN n.notice END) as notice_3d"),
                DB::raw("MAX(CASE WHEN n.notice_type = 9 THEN n.notice END) as notice_4a"),
                DB::raw("MAX(CASE WHEN n.notice_type = 10 THEN n.notice END) as notice_5a")
            )
            ->leftJoinSub($latestNoticesQuery, 'n', 'file_cases.id', '=', 'n.file_case_id')
            ->join('assign_cases', 'assign_cases.case_id', '=', 'file_cases.id')
            ->where('file_cases.status', 1)
            ->groupBy('file_cases.id', 'file_cases.case_type', 'file_cases.case_number', 'file_cases.loan_number', 'file_cases.status', 'file_cases.created_at')
            ->get();         

            return Datatables::of($data)
            ->editColumn('case_type', fn ($row) => config('constant.case_type')[$row->case_type] ?? 'Unknown')
            ->editColumn('created_at', fn ($row) => \Carbon\Carbon::parse($row->created_at)->format('d M, Y'))
            ->editColumn('status', fn ($row) => $row->status == 1
                ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>'
                : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>')
                ->editColumn('notice_1', function ($row) {
                    return $row->notice_1
                        ? '<a href="' . asset('storage/' . $row->notice_1) . '" target="_blank">
                                <img src="' . asset('public/assets/img/pdf.png') . '" height="30" alt="PDF File" />
                           </a>'
                        : '--';
                })
                ->editColumn('notice_1a', function ($row) {
                    return $row->notice_1a
                        ? '<a href="' . asset('storage/' . $row->notice_1a) . '" target="_blank">
                                <img src="' . asset('public/assets/img/pdf.png') . '" height="30" alt="PDF File" />
                           </a>'
                        : '--';
                })
                ->editColumn('notice_1b', function ($row) {
                    return $row->notice_1b
                        ? '<a href="' . asset('storage/' . $row->notice_1b) . '" target="_blank">
                                <img src="' . asset('public/assets/img/pdf.png') . '" height="30" alt="PDF File" />
                           </a>'
                        : '--';
                })
                ->editColumn('notice_2b', function ($row) {
                    return $row->notice_2b
                        ? '<a href="' . asset('storage/' . $row->notice_2b) . '" target="_blank">
                                <img src="' . asset('public/assets/img/pdf.png') . '" height="30" alt="PDF File" />
                           </a>'
                        : '--';
                })
                ->editColumn('notice_3a', function ($row) {
                    return $row->notice_3a
                        ? '<a href="' . asset('storage/' . $row->notice_3a) . '" target="_blank">
                                <img src="' . asset('public/assets/img/pdf.png') . '" height="30" alt="PDF File" />
                           </a>'
                        : '--';
                })
                ->editColumn('notice_3b', function ($row) {
                    return $row->notice_3b
                        ? '<a href="' . asset('storage/' . $row->notice_3b) . '" target="_blank">
                                <img src="' . asset('public/assets/img/pdf.png') . '" height="30" alt="PDF File" />
                           </a>'
                        : '--';
                })
                ->editColumn('notice_3c', function ($row) {
                    return $row->notice_3c
                        ? '<a href="' . asset('storage/' . $row->notice_3c) . '" target="_blank">
                                <img src="' . asset('public/assets/img/pdf.png') . '" height="30" alt="PDF File" />
                           </a>'
                        : '--';
                })
                ->editColumn('notice_3d', function ($row) {
                    return $row->notice_3d
                        ? '<a href="' . asset('storage/' . $row->notice_3d) . '" target="_blank">
                                <img src="' . asset('public/assets/img/pdf.png') . '" height="30" alt="PDF File" />
                           </a>'
                        : '--';
                })
                ->editColumn('notice_4a', function ($row) {
                    return $row->notice_4a
                        ? '<a href="' . asset('storage/' . $row->notice_4a) . '" target="_blank">
                                <img src="' . asset('public/assets/img/pdf.png') . '" height="30" alt="PDF File" />
                           </a>'
                        : '--';
                })
                ->editColumn('notice_5a', function ($row) {
                    return $row->notice_5a
                        ? '<a href="' . asset('storage/' . $row->notice_5a) . '" target="_blank">
                                <img src="' . asset('public/assets/img/pdf.png') . '" height="30" alt="PDF File" />
                           </a>'
                        : '--';
                })
            ->rawColumns(['status', 'notice_1', 'notice_1a', 'notice_1b', 'notice_2b', 'notice_3a', 'notice_3b', 'notice_3c', 'notice_3d', 'notice_4a', 'notice_5a'])
            ->make(true);
            }
        
        return view('drp.allnotices.cashmanagercasenoticelist', compact('drp','title'));
    }


}