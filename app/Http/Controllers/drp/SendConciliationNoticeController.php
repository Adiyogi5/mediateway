<?php

namespace App\Http\Controllers\Drp;

use App\Http\Controllers\Controller;
use App\Models\AssignCase;
use App\Models\FileCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use App\Models\ConciliationNotice;
use App\Models\ConciliatorMeetingRoom;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Exports\ConciliationNoticeExport;
use Maatwebsite\Excel\Facades\Excel;

class SendConciliationNoticeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:drp');
    }

    public function conciliationnoticelist(Request $request): View | JsonResponse | RedirectResponse
    {
        $title = 'Conciliation Notice List';
        $drp = auth('drp')->user();

        // Ensure the user is authenticated and has drp_type == 1
        if (!auth('drp')->check() || auth('drp')->user()->drp_type != 3) {
            return redirect()->route('drp.dashboard')->with('error', 'UnAuthentication Access..!!');
        }
        
        if ($drp->approve_status !== 1) {
            return redirect()->route('drp.dashboard')->withError('DRP is Not Approved by Mediateway.');
        }

        if ($request->ajax()) {
            $data = ConciliationNotice::select(
                    'conciliation_notices.id',
                    'conciliation_notices.conciliation_notice_type',
                    'conciliation_notices.notice_copy',
                    'conciliation_notices.notice_date',
                    'conciliation_notices.file_case_id',
                    'file_cases.case_type',
                    'file_cases.product_type',
                    'file_cases.case_number',
                    'file_cases.loan_number',
                    'file_cases.claimant_first_name',
                    'file_cases.status',
                    'file_cases.created_at',
                    'assign_cases.case_manager_id'
                )
                ->join('file_cases', 'file_cases.id', '=', 'conciliation_notices.file_case_id')
                ->join('assign_cases', 'assign_cases.case_id', '=', 'conciliation_notices.file_case_id')
                ->where('assign_cases.case_manager_id', $drp->id);

            // Filters
            if ($request->filled('case_type')) {
                $data->where('file_cases.case_type', $request->case_type);
            }
            if ($request->filled('product_type')) {
                $data->where('file_cases.product_type', $request->product_type);
            }
            if ($request->filled('conciliation_notice_type')) {
                $data->where('conciliation_notices.conciliation_notice_type', $request->conciliation_notice_type);
            }
            if ($request->filled('case_number')) {
                $data->where('file_cases.case_number', 'like', '%' . $request->case_number . '%');
            }
            if ($request->filled('loan_number')) {
                $data->where('file_cases.loan_number', 'like', '%' . $request->loan_number . '%');
            }
            if ($request->filled('status')) {
                $data->where('file_cases.status', $request->status);
            }
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $data->whereBetween('conciliation_notices.notice_date', [
                    $request->date_from . ' 00:00:00',
                    $request->date_to . ' 23:59:59'
                ]);
            }

            return DataTables::of($data)
                ->editColumn('case_type', fn($row) => config('constant.case_type')[$row->case_type] ?? 'Unknown')
                ->editColumn('product_type', fn($row) => config('constant.product_type')[$row->product_type] ?? 'Unknown')
                ->editColumn('conciliation_notice_type', function ($row) {
                    return $row->conciliation_notice_type == 1
                        ? '<span class="badge bg-warning">Pre-Conciliation</span>'
                        : '<span class="badge bg-secondary">Conciliation</span>';
                })
                ->editColumn('notice_copy', function ($row) {
                    if ($row->notice_copy) {
                        $url = asset('storage/' . $row->notice_copy); // adjust if notice_copy is full URL
                        return '<a href="' . $url . '" target="_blank"><img src="' . asset('public/assets/img/pdf.png') . '" height="30" alt="PDF File" /></a>';
                    }
                    return '<span class="text-muted">N/A</span>';
                })
                ->editColumn('status', function ($row) {
                    return $row->status == 1
                        ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>'
                        : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->editColumn('created_at', fn($row) => $row->created_at->format('d M, Y'))
                ->editColumn('notice_date', fn($row) => \Carbon\Carbon::parse($row->notice_date)->format('d M, Y'))
                ->addColumn('action', function ($row) {
                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    $btn = '<button class="dropdown-item btn btn-sm btn-info bg-info view-details-btn" data-id="' . $row->id . '">View Details</button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('file_cases.created_at', $order);
                })
                ->rawColumns(['case_type', 'product_type', 'conciliation_notice_type', 'notice_copy', 'status', 'action'])
                ->make(true);
        }

        return view('drp.conciliationprocess.conciliationnoticelist', compact('drp','title'));
    }


    // ############################################################################################
    // ########### Show Pre Conciliation Case list according to assigned Case Manager #############
    public function caseList(Request $request)
    {
        $drp = auth('drp')->user();

        if (!$drp) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = FileCase::select(
                    'file_cases.id',
                    'file_cases.case_type',
                    'file_cases.product_type',
                    'file_cases.case_number',
                    'file_cases.loan_number',
                    'file_cases.status',
                    'file_cases.created_at',
                    'assign_cases.case_manager_id'
                )
                ->join('assign_cases', 'assign_cases.case_id', '=', 'file_cases.id')
                ->where('assign_cases.case_manager_id', $drp->id)
                ->where('file_cases.case_type', 3)
                ->whereNotIn('file_cases.id', function($subquery) {
                    $subquery->select('file_case_id')
                            ->from('conciliation_notices')
                            ->whereNull('deleted_at');
                });

        // Apply filters
        if ($request->filled('case_type')) {
            $query->where('file_cases.case_type', $request->case_type);
        }

        if ($request->filled('product_type')) {
            $query->where('file_cases.product_type', $request->product_type);
        }

        if ($request->filled('case_number')) {
            $query->where('file_cases.case_number', 'like', '%' . $request->case_number . '%');
        }

        if ($request->filled('loan_number')) {
            $query->where('file_cases.loan_number', 'like', '%' . $request->loan_number . '%');
        }

        if ($request->filled('status')) {
            $query->where('file_cases.status', $request->status);
        }
        
        if ($request->filled('date_from') && $request->filled('date_to')) {
                    $query->whereBetween('file_cases.created_at', [
                        $request->date_from . ' 00:00:00',
                        $request->date_to . ' 23:59:59'
                    ]);
                }

        $cases = $query->get()->map(function ($item) {
            return [
                'id' => $item->id,
                'case_type' => config('constant.case_type')[$item->case_type] ?? 'N/A',
                'product_type' => config('constant.product_type')[$item->product_type] ?? 'N/A',
                'case_number' => $item->case_number,
                'loan_number' => $item->loan_number,
                'status' => $item->status == 1
                    ? '<span class="badge bg-success">Active</span>'
                    : '<span class="badge bg-danger">Inactive</span>',
                'created_at' => $item->created_at->format('d-m-Y'),
            ];
        });

        return response()->json(['data' => $cases]);
    }


    public function sendpreconciliationNotices(Request $request)
    {
        $drp = auth('drp')->user();
        if (!$drp) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $caseIds = $request->input('file_case_ids');
        $noticeDate = $request->input('send_notice_date');

        if (empty($caseIds) || !is_array($caseIds)) {
            return response()->json(['error' => 'No case IDs received.'], 422);
        }

        $notices = [];

        foreach ($caseIds as $caseId) {
            $notices[] = [
                'file_case_id' => $caseId,
                'conciliation_notice_type' => 1,
                'notice_date' => $noticeDate,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        ConciliationNotice::insert($notices);

        return response()->json(['message' => 'Notices created successfully.']);
    }


    // ########################################################################################
    // ########### Show Conciliation Case list according to Conciliation Meeting #############
    public function conciliationcaselist(Request $request)
    {
        $drp = auth('drp')->user();
        if (!$drp) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($request->ajax()) {
            $data = ConciliationNotice::select(
                    'conciliation_notices.id',
                    'conciliation_notices.conciliation_notice_type',
                    'conciliation_notices.notice_copy',
                    'conciliation_notices.file_case_id',
                    'file_cases.case_type',
                    'file_cases.product_type',
                    'file_cases.case_number',
                    'file_cases.loan_number',
                    'file_cases.status',
                    'file_cases.created_at',
                    'assign_cases.case_manager_id'
                )
                ->join('file_cases', 'file_cases.id', '=', 'conciliation_notices.file_case_id')
                ->join('assign_cases', 'assign_cases.case_id', '=', 'conciliation_notices.file_case_id')
                ->whereDate('conciliation_notices.notice_date', '>=', Carbon::now()->subDays(7)->toDateString())
                ->whereRaw("
                    NOT EXISTS (
                        SELECT 1 FROM conciliator_meeting_rooms
                        WHERE FIND_IN_SET(conciliation_notices.file_case_id, conciliator_meeting_rooms.meeting_room_case_id)
                    )
                ")
                ->where('conciliation_notices.conciliation_notice_type', 1)
                ->where('assign_cases.case_manager_id', $drp->id);

            // Filters
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
            if ($request->filled('status')) {
                $data->where('file_cases.status', $request->status);
            }
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $data->whereBetween('file_cases.created_at', [
                    $request->date_from . ' 00:00:00',
                    $request->date_to . ' 23:59:59'
                ]);
            }

            return DataTables::of($data)
                ->addColumn('file_case_id', function ($row) {
                    return $row->file_case_id; // ✅ explicitly output this
                })
                ->editColumn('case_type', fn($row) => config('constant.case_type')[$row->case_type] ?? 'Unknown')
                ->editColumn('product_type', fn($row) => config('constant.product_type')[$row->product_type] ?? 'Unknown')
                ->editColumn('conciliation_notice_type', function ($row) {
                    return $row->conciliation_notice_type == 1
                        ? '<span class="badge bg-warning">Pre-Conciliation</span>'
                        : '<span class="badge bg-secondary">Conciliation</span>';
                })
                ->editColumn('notice_copy', function ($row) {
                    if ($row->notice_copy) {
                        $url = asset('storage/' . $row->notice_copy); // adjust if notice_copy is full URL
                        return '<a href="' . $url . '" target="_blank"><img src="' . asset('public/assets/img/pdf.png') . '" height="30" alt="PDF File" /></a>';
                    }
                    return '<span class="text-muted">N/A</span>';
                })
                ->editColumn('status', function ($row) {
                    return $row->status == 1
                        ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>'
                        : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->editColumn('created_at', fn($row) => $row->created_at->format('d M, Y'))
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('file_cases.created_at', $order);
                })
                ->rawColumns(['case_type', 'product_type', 'conciliation_notice_type', 'notice_copy', 'status'])
                ->make(true);
        }
    }


    public function sendconciliationNotices(Request $request)
    {
        $drp = auth('drp')->user();
        if (!$drp) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $caseIds = $request->input('file_case_ids');
        if (empty($caseIds) || !is_array($caseIds)) {
            return response()->json(['error' => 'No case IDs provided.'], 422);
        }

        $request->validate([
            'file_case_ids' => 'required|array|min:1',
            'date' => 'required|date|after_or_equal:today',
            'time' => ['required', function ($attribute, $value, $fail) use ($request) {
                if ($request->date === now()->format('Y-m-d') && $value < now()->format('H:i')) {
                    $fail('The time must not be in the past.');
                }
            }],
        ]);

        // Step 1: Group case_ids by their conciliator_id
        $assignments = AssignCase::whereIn('case_id', $caseIds)
            ->select('case_id', 'conciliator_id')
            ->get()
            ->groupBy('conciliator_id');

        // Step 2: Get the last room number
        $room_id_prefix = 'ORG-CON-MEETING';
        $lastRoom = ConciliatorMeetingRoom::where('room_id', 'like', $room_id_prefix . '-%')
            ->orderBy('id', 'desc')->first();

        $lastNumber = $lastRoom
            ? (int) str_replace($room_id_prefix . '-', '', $lastRoom->room_id)
            : 0;

        $meetingRooms = [];

        // Step 3: Create a new room for each conciliator with their assigned cases
        foreach ($assignments as $conciliatorId => $cases) {
            $lastNumber++;
            $room_id = $room_id_prefix . '-' . str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

            $caseIdsForConciliator = $cases->pluck('case_id')->toArray();

            $meetingRooms[] = [
                'room_id' => $room_id,
                'meeting_room_case_id' => implode(',', $caseIdsForConciliator),
                'conciliator_id' => $conciliatorId,
                'date' => $request->date,
                'time' => $request->time,
                'status' => 0
            ];
        }
        ConciliatorMeetingRoom::insert($meetingRooms);


        //Send Conciliation Notice Data
        $notices = [];

        foreach ($caseIds as $caseId) {
            $notices[] = [
                'file_case_id' => $caseId,
                'conciliation_notice_type' => 2,
                'notice_date' => Carbon::now(),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        ConciliationNotice::insert($notices);

        return response()->json(['success' => true, 'message' => 'Conciliator Meeting rooms created successfully.']);
    }

    
    public function getAllFilteredConciliationCaseIds(Request $request)
    {
        $drp = auth('drp')->user();
        if (!$drp) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = ConciliationNotice::select('file_cases.id as file_case_id')
            ->join('file_cases', 'file_cases.id', '=', 'conciliation_notices.file_case_id')
            ->join('assign_cases', 'assign_cases.case_id', '=', 'conciliation_notices.file_case_id')
            ->whereDate('conciliation_notices.notice_date', '>=', Carbon::now()->subDays(7)->toDateString())
            ->whereRaw("
                NOT EXISTS (
                    SELECT 1 FROM conciliator_meeting_rooms
                    WHERE FIND_IN_SET(conciliation_notices.file_case_id, conciliator_meeting_rooms.meeting_room_case_id)
                )
            ")
            ->where('conciliation_notices.conciliation_notice_type', 1)
            ->where('assign_cases.case_manager_id', $drp->id);

        // Apply filters (same as in conciliationcaselist)
        if ($request->filled('product_type')) {
            $data->where('file_cases.product_type', $request->product_type);
        }
        if ($request->filled('case_number')) {
            $data->where('file_cases.case_number', 'like', '%' . $request->case_number . '%');
        }
        if ($request->filled('loan_number')) {
            $data->where('file_cases.loan_number', 'like', '%' . $request->loan_number . '%');
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

        return response()->json([
            'case_ids' => $data->pluck('file_case_id')->unique()->values()
        ]);
    }


    // ########### Modal - Show Case Details #############
    public function getConciliationNotice($id)
    {
        $notice = ConciliationNotice::with('fileCase')
            ->where('id', $id)
            ->first();
       
        if (!$notice) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        return response()->json([
            'id' => $notice->id,

            'case_number' => $notice->fileCase->case_number ?? '-',
            'loan_number' => $notice->fileCase->loan_number ?? '-',
            'respondent_first_name' => $notice->fileCase->respondent_first_name ?? '-',
            'respondent_email' => $notice->fileCase->respondent_email ?? '-',
            'respondent_mobile' => $notice->fileCase->respondent_mobile ?? '-',

            'case_type' => config('constant.case_type')[$notice->fileCase->case_type] ?? 'Unknown',
            'product_type' => config('constant.product_type')[$notice->fileCase->product_type] ?? 'Unknown',

            'conciliation_notice_type' => $notice->conciliation_notice_type == 1 ? 'Pre-Conciliation' : 'Conciliation',
            'notice_date' => $notice->notice_date ? \Carbon\Carbon::parse($notice->notice_date)->format('d M, Y') : '-',
            'notice_copy' => asset('storage/' . $notice->notice_copy) ?? '-',
            'email_status' => match ($notice->email_status) {
                1 => '<span class="badge bg-success">Send</span>',
                0 => '<span class="badge bg-warning text-dark">Pending</span>',
                2 => '<span class="badge bg-danger">Failed</span>',
                default => '<span class="badge bg-secondary">Unknown</span>',
            },
            'notice_send_date' => $notice->notice_send_date ? \Carbon\Carbon::parse($notice->notice_send_date)->format('d M, Y h:i A') : '-',

            'whatsapp_notice_status' => match ($notice->whatsapp_notice_status) {
                1 => '<span class="badge bg-success">Send</span>',
                0 => '<span class="badge bg-warning text-dark">Pending</span>',
                2 => '<span class="badge bg-danger">Failed</span>',
                default => '<span class="badge bg-secondary">Unknown</span>',
            },
            'whatsapp_dispatch_datetime' => $notice->whatsapp_dispatch_datetime ? \Carbon\Carbon::parse($notice->whatsapp_dispatch_datetime)->format('d M, Y h:i A') : '-',

            'sms_status' => match ($notice->sms_status) {
                1 => '<span class="badge bg-success">Send</span>',
                0 => '<span class="badge bg-warning text-dark">Pending</span>',
                2 => '<span class="badge bg-danger">Failed</span>',
                default => '<span class="badge bg-secondary">Unknown</span>',
            },
            'sms_send_date' => $notice->sms_send_date ? \Carbon\Carbon::parse($notice->sms_send_date)->format('d M, Y h:i A') : '-',
        ]);
    }


    // ########### Export Notice with Send Details #############
    public function exportconciliationNotice(Request $request)
    {
        $date = date('d-m-Y_h:i:sA');
        $filename = 'conciliation_notice_list_' . $date . '.xlsx';

        return Excel::download(new ConciliationNoticeExport($request), $filename);
    }

}