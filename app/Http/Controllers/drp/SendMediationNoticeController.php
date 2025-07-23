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
use App\Models\MediationNotice;
use App\Models\MediatorMeetingRoom;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Exports\MediationNoticeExport;
use App\Models\MediationNoticeMaster;
use Maatwebsite\Excel\Facades\Excel;

class SendMediationNoticeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:drp');
    }

    public function mediationnoticemasterlist(Request $request): View | JsonResponse | RedirectResponse
    {
        $title = 'Mediation Notice Master List';
        $drp = auth('drp')->user();

        // Ensure the user is authenticated and has drp_type == 1
        if (!auth('drp')->check() || auth('drp')->user()->drp_type != 3) {
            return redirect()->route('drp.dashboard')->with('error', 'UnAuthentication Access..!!');
        }
        
        if ($drp->approve_status !== 1) {
            return redirect()->route('drp.dashboard')->withError('DRP is Not Approved by Mediateway.');
        }

        if ($request->ajax()) {
            $data = MediationNoticeMaster::select(
                    'mediation_notice_masters.id',
                    'mediation_notice_masters.mediation_notice_type',
                    'mediation_notice_masters.file_name',
                    'mediation_notice_masters.date',
                    'mediation_notice_masters.case_manager_id',
                    'organizations.name as organization_name',
                )
                ->leftJoin('organizations', 'organizations.id', '=', 'mediation_notice_masters.uploaded_by')
                ->where('mediation_notice_masters.case_manager_id', $drp->id)
                ->whereNull('mediation_notice_masters.deleted_at');

            // Filters
            if ($request->filled('mediation_notice_type')) {
                $data->where('mediation_notice_masters.mediation_notice_type', $request->mediation_notice_type);
            }
            if ($request->filled('date_from') && $request->filled('date_to')) {
                $data->whereBetween('mediation_notice_masters.date', [
                    $request->date_from . ' 00:00:00',
                    $request->date_to . ' 23:59:59'
                ]);
            }
       
            return DataTables::of($data)
                ->addColumn('email_count', function ($row) {
                    $total = MediationNotice::where('mediation_master_id', $row->id)->count();
                    $sent = MediationNotice::where('mediation_master_id', $row->id)
                        ->where('email_status', 1)
                        ->count();
                    return '<span class="badge bg-secondary text-white py-1 px-2">' . $sent . '/' . $total . '</span>';
                })
                ->addColumn('whatsapp_count', function ($row) {
                    $total = MediationNotice::where('mediation_master_id', $row->id)->count();
                    $sent = MediationNotice::where('mediation_master_id', $row->id)
                        ->where('whatsapp_notice_status', 1)
                        ->count();
                    return '<span class="badge bg-secondary text-white py-1 px-2">' . $sent . '/' . $total . '</span>';
                })
                ->addColumn('sms_count', function ($row) {
                    $total = MediationNotice::where('mediation_master_id', $row->id)->count();
                    $sent = MediationNotice::where('mediation_master_id', $row->id)
                        ->where('sms_status', 1)
                        ->count();
                    return '<span class="badge bg-secondary text-white py-1 px-2">' . $sent . '/' . $total . '</span>';
                })
                ->editColumn('mediation_notice_type', function ($row) {
                    return $row->mediation_notice_type == 1
                        ? '<span class="badge bg-warning">Pre-Mediation</span>'
                        : '<span class="badge bg-secondary">Mediation</span>';
                })
                ->editColumn('date', fn($row) => \Carbon\Carbon::parse($row->date)->format('d M, Y'))
                ->addColumn('action', function ($row) {
                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    $btn .= '<a class="dropdown-item" href="' . route('drp.mediationprocess.mediationnoticelist', ['master_id' => $row->id]) . '">View Details</a>';
                    $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    return $btn;
                })
                ->orderColumn('date', function ($query, $order) {
                    $query->orderBy('mediation_notice_masters.date', $order);
                })
                ->rawColumns(['mediation_notice_type', 'action', 'email_count', 'whatsapp_count', 'sms_count'])
                ->make(true);
        }

        return view('drp.mediationprocess.mediationnoticemasterlist', compact('drp','title'));
    }


    public function mediationnoticelist(Request $request, $master_id): View | JsonResponse | RedirectResponse
    {
        $title = 'Mediation Notice List';
        $drp = auth('drp')->user();

        // Ensure the user is authenticated and has drp_type == 1
        if (!auth('drp')->check() || auth('drp')->user()->drp_type != 3) {
            return redirect()->route('drp.dashboard')->with('error', 'UnAuthentication Access..!!');
        }
        
        if ($drp->approve_status !== 1) {
            return redirect()->route('drp.dashboard')->withError('DRP is Not Approved by Mediateway.');
        }

        if ($request->ajax()) {
            $data = MediationNotice::select(
                    'mediation_notices.id',
                    'mediation_notices.mediation_notice_type',
                    'mediation_notices.notice_copy',
                    'mediation_notices.notice_date',
                    'mediation_notices.file_case_id',
                    'file_cases.case_type',
                    'file_cases.product_type',
                    'file_cases.case_number',
                    'file_cases.loan_number',
                    'file_cases.claimant_first_name',
                    'file_cases.status',
                    'file_cases.created_at',
                    'assign_cases.case_manager_id'
                )
                ->join('file_cases', 'file_cases.id', '=', 'mediation_notices.file_case_id')
                ->join('assign_cases', 'assign_cases.case_id', '=', 'mediation_notices.file_case_id')
                ->where('mediation_notices.mediation_master_id', $master_id)
                ->where('assign_cases.case_manager_id', $drp->id)
                ->whereNull('mediation_notices.deleted_at')
                ->whereNull('file_cases.deleted_at');

            // Filters
            if ($request->filled('case_type')) {
                $data->where('file_cases.case_type', $request->case_type);
            }
            if ($request->filled('product_type')) {
                $data->where('file_cases.product_type', $request->product_type);
            }
            if ($request->filled('mediation_notice_type')) {
                $data->where('mediation_notices.mediation_notice_type', $request->mediation_notice_type);
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
                $data->whereBetween('mediation_notices.notice_date', [
                    $request->date_from . ' 00:00:00',
                    $request->date_to . ' 23:59:59'
                ]);
            }

            return DataTables::of($data)
                ->editColumn('case_type', fn($row) => config('constant.case_type')[$row->case_type] ?? 'Unknown')
                ->editColumn('product_type', fn($row) => config('constant.product_type')[$row->product_type] ?? 'Unknown')
                ->editColumn('mediation_notice_type', function ($row) {
                    return $row->mediation_notice_type == 1
                        ? '<span class="badge bg-warning">Pre-Mediation</span>'
                        : '<span class="badge bg-secondary">Mediation</span>';
                })
                ->editColumn('notice_copy', function ($row) {
                    if ($row->notice_copy) {
                        $url = asset('storage/' . $row->notice_copy); // adjust if notice_copy is full URL
                        return '<a href="' . $url . '" target="_blank"><img src="' . asset('assets/img/pdf.png') . '" height="30" alt="PDF File" /></a>';
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
                ->rawColumns(['case_type', 'product_type', 'mediation_notice_type', 'notice_copy', 'status', 'action'])
                ->make(true);
        }

        return view('drp.mediationprocess.mediationnoticelist', compact('drp', 'title', 'master_id'));
    }


    // ############################################################################################
    // ########### Show Pre Mediation Case list according to assigned Case Manager #############
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
                ->where('file_cases.case_type', 2)
                ->whereNotIn('file_cases.id', function($subquery) {
                    $subquery->select('file_case_id')
                            ->from('mediation_notices')
                            ->whereNull('deleted_at');
                })->distinct();

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

    
    public function sendpremediationNotices(Request $request)
    {
        $drp = auth('drp')->user();
        if (!$drp) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->json()->all(); 

        $caseIds = $data['file_case_ids'] ?? [];
        $noticeDate = $data['send_notice_date'] ?? null;

        if (empty($caseIds) || !is_array($caseIds)) {
            return response()->json(['error' => 'No case IDs received.'], 422);
        }

        if (empty($noticeDate)) {
            return response()->json(['error' => 'Notice date is required.'], 422);
        }

        $chunkSize = 2000;
        $now = now();
        $successCount = 0;

        collect($caseIds)->chunk($chunkSize)->each(function ($caseIdChunk) use ($drp, $noticeDate, $now, &$successCount) {
            $groupedCases = FileCase::whereIn('id', $caseIdChunk)
                ->select('id', 'organization_id')
                ->get()
                ->groupBy('organization_id');

            foreach ($groupedCases as $organizationId => $cases) {
                DB::beginTransaction();
                try {
                    // ✅ 1. Create Master Record
                    $master = new MediationNoticeMaster();
                    $master->case_manager_id = $drp->id;
                    $master->mediation_notice_type = 1;
                    $master->uploaded_by = $organizationId;
                    $master->file_name = 'Pre-Mediation-Notice-' . strtoupper($now->format('d-m-Y_h:ia'));
                    $master->date = $now;
                    $master->save();

                    // ✅ 2. Prepare and Chunk Child Records
                    $notices = $cases->map(function ($case) use ($master, $noticeDate, $now) {
                        return [
                            'mediation_master_id' => $master->id,
                            'file_case_id' => $case->id,
                            'mediation_notice_type' => 1,
                            'notice_date' => $noticeDate,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    });

                    $chunkSize = 2000;
                    $notices->chunk($chunkSize)->each(function ($chunked) {
                        MediationNotice::insert($chunked->toArray());
                    });

                    DB::commit();
                    $successCount++;
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Error creating Pre-Mediation notices for Org ID {$organizationId}: " . $e->getMessage());
                    continue;
                }
            }
        });

        return response()->json([
            'message' => 'Notices created successfully.',
            'processed_batches' => $successCount
        ]);
    }


    // ########################################################################################
    // ########### Show Mediation Case list according to Mediation Meeting #############
    public function mediationcaselist(Request $request)
    {
        $drp = auth('drp')->user();
        if (!$drp) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if ($request->ajax()) {
            $data = MediationNotice::select(
                    'mediation_notices.id',
                    'mediation_notices.mediation_notice_type',
                    'mediation_notices.notice_copy',
                    'mediation_notices.notice_date',
                    'mediation_notices.file_case_id',
                    'file_cases.case_type',
                    'file_cases.product_type',
                    'file_cases.case_number',
                    'file_cases.loan_number',
                    'file_cases.status',
                    'file_cases.created_at',
                    'assign_cases.case_manager_id'
                )
                ->join('file_cases', 'file_cases.id', '=', 'mediation_notices.file_case_id')
                ->join('assign_cases', 'assign_cases.case_id', '=', 'mediation_notices.file_case_id')
                ->whereDate('mediation_notices.notice_date', '<', Carbon::now()->subDays(7)->toDateString())
                ->whereRaw("
                    NOT EXISTS (
                        SELECT 1 FROM mediator_meeting_rooms
                        WHERE FIND_IN_SET(mediation_notices.file_case_id, mediator_meeting_rooms.meeting_room_case_id)
                    )
                ")
                ->where('mediation_notices.mediation_notice_type', 1)
                ->where('assign_cases.case_manager_id', $drp->id)
                ->whereNull('mediation_notices.deleted_at')
                ->whereNull('file_cases.deleted_at')->distinct();

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
                ->editColumn('mediation_notice_type', function ($row) {
                    return $row->mediation_notice_type == 1
                        ? '<span class="badge bg-warning">Pre-Mediation</span>'
                        : '<span class="badge bg-secondary">Mediation</span>';
                })
                ->editColumn('notice_copy', function ($row) {
                    if ($row->notice_copy) {
                        $url = asset('storage/' . $row->notice_copy); // adjust if notice_copy is full URL
                        return '<a href="' . $url . '" target="_blank"><img src="' . asset('assets/img/pdf.png') . '" height="30" alt="PDF File" /></a>';
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
                ->rawColumns(['case_type', 'product_type', 'mediation_notice_type', 'notice_copy', 'status'])
                ->make(true);
        }
    }


    public function sendmediationNotices(Request $request)
    {
        $drp = auth('drp')->user();
        if (!$drp) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->json()->all();

        $caseIds = $data['file_case_ids'] ?? [];
        $date = $data['date'] ?? null;
        $time = $data['time'] ?? null;
        $action = $data['action'] ?? 'send'; // default to 'send'

        $validator = Validator::make($data, [
            'file_case_ids' => 'required|array|min:1',
            'date' => 'required|date|after_or_equal:today',
            'time' => ['required', function ($attribute, $value, $fail) use ($date) {
                if ($date === now()->format('Y-m-d') && $value < now()->format('H:i')) {
                    $fail('The time must not be in the past.');
                }
            }],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // ✅ Group by mediator_id
        $assignments = AssignCase::whereIn('case_id', $caseIds)
            ->select('case_id', 'mediator_id')
            ->get()
            ->groupBy('mediator_id');

        // ✅ Get last room number
        $room_id_prefix = 'ORG-CON-MEETING';
        $lastRoom = MediatorMeetingRoom::where('room_id', 'like', $room_id_prefix . '-%')
            ->orderBy('id', 'desc')->first();
        $lastNumber = $lastRoom ? (int) str_replace($room_id_prefix . '-', '', $lastRoom->room_id) : 0;

        $meetingRooms = [];

        foreach ($assignments as $mediatorId => $cases) {
            $lastNumber++;
            $room_id = $room_id_prefix . '-' . str_pad($lastNumber, 7, '0', STR_PAD_LEFT);

            $caseIdsForMediator = $cases->pluck('case_id')->toArray();

            $meetingRooms[] = [
                'room_id' => $room_id,
                'meeting_room_case_id' => implode(',', $caseIdsForMediator),
                'mediator_id' => $mediatorId,
                'date' => $date,
                'time' => $time,
                'status' => 0,
            ];
        }
        MediatorMeetingRoom::insert($meetingRooms);


        // ✅ If action is 'decline', skip notice creation
        if ($action === 'decline') {
            return response()->json([
                'success' => true,
                'message' => 'Meeting rooms created without sending notices.'
            ]);
        }

        // ✅ If action is 'send', also create notices
        $chunkSize = 2000;

        collect($caseIds)->chunk($chunkSize)->each(function ($caseIdChunk) use ($drp) {
            $groupedCases = FileCase::whereIn('id', $caseIdChunk)
                ->select('id', 'organization_id')
                ->get()
                ->groupBy('organization_id');

            foreach ($groupedCases as $orgId => $cases) {
                DB::beginTransaction();
                try {
                    $master = new MediationNoticeMaster();
                    $master->case_manager_id = $drp->id;
                    $master->mediation_notice_type = 2;
                    $master->uploaded_by = $orgId;
                    $master->meeting_room = 1;
                    $master->file_name = 'Mediation-Notice-' . now()->format('d-m-Y_h:ia');
                    $master->date = now();
                    $master->save();

                    $notices = $cases->map(function ($case) use ($master) {
                        return [
                            'mediation_master_id' => $master->id,
                            'file_case_id' => $case->id,
                            'mediation_notice_type' => 2,
                            'notice_date' => now(),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    });

                    $chunkSize = 2000;
                    $notices->chunk($chunkSize)->each(function ($chunked) {
                        MediationNotice::insert($chunked->toArray());
                    });

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Error processing org ID {$orgId}: " . $e->getMessage());
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Meeting rooms and Mediation Notices created successfully.'
        ]);
    }

    
    // ########## Get all ids for Mediation notice send ############
    public function getAllFilteredMediationCaseIds(Request $request)
    {
        $drp = auth('drp')->user();
        if (!$drp) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = MediationNotice::select('file_cases.id as file_case_id')
            ->join('file_cases', 'file_cases.id', '=', 'mediation_notices.file_case_id')
            ->join('assign_cases', 'assign_cases.case_id', '=', 'mediation_notices.file_case_id')
            ->whereDate('mediation_notices.notice_date', '<', Carbon::now()->subDays(7)->toDateString())
                ->whereRaw("
                    NOT EXISTS (
                        SELECT 1 FROM mediator_meeting_rooms
                        WHERE FIND_IN_SET(mediation_notices.file_case_id, mediator_meeting_rooms.meeting_room_case_id)
                    )
                ")
            ->where('mediation_notices.mediation_notice_type', 1)
            ->where('assign_cases.case_manager_id', $drp->id)
            ->whereNull('mediation_notices.deleted_at')
            ->whereNull('file_cases.deleted_at')->distinct();

        // Apply filters (same as in mediationcaselist)
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
    public function getMediationNotice($id)
    {
        $notice = MediationNotice::with('fileCase')
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

            'mediation_notice_type' => $notice->mediation_notice_type == 1 ? 'Pre-Mediation' : 'Mediation',
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
    public function exportmediationNotice(Request $request)
    {
        $date = date('d-m-Y_h:i:sA');
        $filename = 'mediation_notice_list_' . $date . '.xlsx';

        return Excel::download(new MediationNoticeExport($request, $request->master_id), $filename);
    }

    
    public function deleteMediationMaster($id)
    {
        $drp = auth('drp')->user();
        if (!$drp) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            DB::beginTransaction();

            MediationNotice::where('mediation_master_id', $id)->delete();
            MediationNoticeMaster::where('id', $id)->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Master record and related notices deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to delete.']);
        }
    }


}