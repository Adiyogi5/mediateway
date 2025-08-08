<?php

namespace App\Http\Controllers\Drp;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\Drp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Config;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CaseNoticeListController extends Controller
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
        if ($drp->approve_status !== 1) {
            return redirect()->route('drp.dashboard')->withError('DRP is Not Approved by Mediateway.');
        }
        if ($drp->drp_type !== 1) {
            return redirect()->route('drp.dashboard')->withError('Unauthorized access.');
        }

        if (!$request->ajax()) {
            ################## Profile Incomplete Start ##################
            $drpdata = Drp::with('drpDetail')->where('id', $drp->id)->first();

            // Required fields to check
            $requiredFields = [
                'name', 'email', 'mobile', 'state_id', 'city_id', 'pincode', 'image',
                'signature_drp', 'dob', 'nationality', 'gender', 'address1', 'profession', 'specialization',
            ];

            // Check if any field is null or empty
            $missingFields = collect($requiredFields)->filter(fn($field) => empty($drpdata->$field));

            if ($missingFields->isNotEmpty()) {
                return view('drp.allnotices.arbitratorcasenoticelist', compact('drpdata', 'drp', 'title'))
                    ->with('showProfilePopup', true);
            }
            ################## Profile Incomplete End ##################
        }
        
        if ($request->ajax()) {
            // Get the latest notices per file_case_id and notice_type
            $latestNoticesQuery = DB::table('notices as n1')
                ->select(
                    'n1.file_case_id',
                    'n1.notice_type',
                    'n1.created_at',
                    'n1.notice',
                    'n1.notice_date',
                    'n1.email_status',
                    'n1.whatsapp_notice_status',
                    'n1.sms_status'
                )
                ->whereDate('n1.notice_date', '<=', now())
                ->join(DB::raw('
                    (SELECT file_case_id, notice_type, MAX(created_at) as max_created
                    FROM notices
                    GROUP BY file_case_id, notice_type) as n2
                '), function ($join) {
                    $join->on('n1.file_case_id', '=', 'n2.file_case_id')
                        ->on('n1.notice_type', '=', 'n2.notice_type')
                        ->on('n1.created_at', '=', 'n2.max_created');
                });

            // List of all notice types for dynamic handling
            $noticeTypes = [
                'notice_1' => 1,
                'notice_1a' => 2,
                'notice_1b' => 3,
                'notice_2b' => 4,
                'notice_3a' => 5,
                'notice_3b' => 6,
                'notice_3c' => 7,
                'notice_3d' => 8,
                'notice_4a' => 9,
                'notice_5a' => 10,
            ];

            // Main query with left join and grouping
            $selectFields = [
                'file_cases.id',
                'file_cases.case_type',
                'file_cases.case_number',
                'file_cases.loan_number',
                'file_cases.status',
                'file_cases.created_at',
            ];

            // Add the notice types dynamically to the select
            foreach ($noticeTypes as $alias => $type) {
                $selectFields[] = DB::raw("MAX(CASE WHEN n.notice_type = $type THEN n.notice END) as $alias");
                $selectFields[] = DB::raw("MAX(CASE WHEN n.notice_type = $type THEN n.notice_date END) as {$alias}_date");
                $selectFields[] = DB::raw("MAX(CASE WHEN n.notice_type = $type THEN n.email_status END) as {$alias}_email_status");
                $selectFields[] = DB::raw("MAX(CASE WHEN n.notice_type = $type THEN n.whatsapp_notice_status END) as {$alias}_whatsapp_notice_status");
                $selectFields[] = DB::raw("MAX(CASE WHEN n.notice_type = $type THEN n.sms_status END) as {$alias}_sms_status");
            }

            $dataQuery = DB::table('file_cases')
                ->select($selectFields)
                ->leftJoinSub($latestNoticesQuery, 'n', 'file_cases.id', '=', 'n.file_case_id')
                ->join('assign_cases', 'assign_cases.case_id', '=', 'file_cases.id')
                ->where('assign_cases.arbitrator_id', $drp->id)
                ->where('file_cases.case_type', 1)
                ->where('file_cases.status', 1);

            // Apply filters if present
            if ($request->filled('case_type')) {
                $dataQuery->where('file_cases.case_type', $request->case_type);
            }

            if ($request->filled('case_number')) {
                $dataQuery->where('file_cases.case_number', 'like', '%' . $request->case_number . '%');
            }

            if ($request->filled('loan_number')) {
                $dataQuery->where('file_cases.loan_number', 'like', '%' . $request->loan_number . '%');
            }

            if ($request->filled('status')) {
                $dataQuery->where('file_cases.status', $request->status);
            }

            if ($request->filled('from_date') && $request->filled('to_date')) {
                $dataQuery->whereBetween('file_cases.created_at', [
                    $request->from_date . ' 00:00:00',
                    $request->to_date . ' 23:59:59'
                ]);
            }

            $data = $dataQuery
                ->orderBy('file_cases.created_at', 'DESC')
                ->groupBy(
                    'file_cases.id',
                    'file_cases.case_type',
                    'file_cases.case_number',
                    'file_cases.loan_number',
                    'file_cases.status',
                    'file_cases.created_at'
                )
                ->get();
 
            // DataTables setup
            $dataTable = Datatables::of($data)
                ->editColumn('case_type', fn($row) => config('constant.case_type')[$row->case_type] ?? 'Unknown')
                ->editColumn('created_at', fn($row) => \Carbon\Carbon::parse($row->created_at)->format('d M, Y'))
                ->editColumn('status', fn($row) => $row->status == 1
                    ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>'
                    : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>'
                );

            // Loop through each notice type and apply `editColumn`
            foreach (array_keys($noticeTypes) as $noticeType) {
                $dataTable->editColumn($noticeType, function ($row) use ($noticeType) {
                    // Generate the PDF link if the file exists
                    $html = $row->$noticeType
                        ? '<a href="' . asset('storage/' . $row->$noticeType) . '" target="_blank">
                                <img src="' . asset('assets/img/pdf.png') . '" height="30" alt="PDF File" />
                        </a>'
                        : '--';

                    // Define specific date, email, and WhatsApp status fields
                    $dateField = $noticeType . '_date';
                    $emailField = $noticeType . '_email_status';
                    $whatsappField = $noticeType . '_whatsapp_notice_status';
                    $smsField = $noticeType . '_sms_status';

                    // Get the values if they exist, otherwise use '--'
                    $noticeDate = !empty($row->$dateField) ? \Carbon\Carbon::parse($row->$dateField)->format('d M, Y') : '--';

                    if ($row->$emailField == 1) {
                        $emailStatus = '<span class="text-success">Sent</span>';
                    } elseif ($row->$emailField == 2) {
                        $emailStatus = '<span class="text-warning">Failed</span>';
                    } else {
                        $emailStatus = '<span class="text-danger">Pending</span>';
                    }

                    if ($row->$whatsappField == 1) {
                        $whatsappStatus = '<span class="text-success">Sent</span>';
                    } elseif ($row->$whatsappField == 2) {
                        $whatsappStatus = '<span class="text-warning">Failed</span>';
                    } else {
                        $whatsappStatus = '<span class="text-danger">Pending</span>';
                    }

                    if ($row->$smsField == 1) {
                        $smsStatus = '<span class="text-success">Sent</span>';
                    } elseif ($row->$smsField == 2) {
                        $smsStatus = '<span class="text-warning">Failed</span>';
                    } else {
                        $smsStatus = '<span class="text-danger">Pending</span>';
                    }

                    // Append the date and status info
                    $html .= '<div class="mt-2">
                                <small>Notice Date: ' . $noticeDate . '</small><br>
                                <small>Email Status: ' . $emailStatus . '</small><br>
                                <small>WhatsApp Status: ' . $whatsappStatus . '</small><br>
                                <small>SMS Status: ' . $smsStatus . '</small>
                            </div>';

                    return $html;
                });
            }

            // Declare raw columns for proper HTML rendering
            $dataTable->rawColumns(array_merge(
                ['status'],
                array_keys($noticeTypes)
            ));

            return $dataTable->make(true);
        }

        return view('drp.allnotices.arbitratorcasenoticelist', compact('drp','title'));
    }


}