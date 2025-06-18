<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\Drp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use \Yajra\Datatables\Datatables;

class DrpListController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            // $data = OrganizationList::query();
            $data = Drp::with('drpDetail')->select('*');

            return Datatables::of($data)
                ->addColumn('drp_type', function ($row) {
                    return config('constant.drp_type')[$row->drp_type] ?? '--';
                })
                ->addColumn('action', function ($row) {
                    $allData = $row->toArray();
                    
                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (Helper::userCan(112, 'can_add')) {
                        $btn .= '<button class="dropdown-item text-warning view-detail" data-id="' . $row->id . '">View Detail</button>';
                    }
                    if (Helper::userCan(112, 'can_edit')) {
                        $btn .= '<button class="dropdown-item text-success approve" data-id="' . $row->id . '">Approve</button>';
                    }
                    if (Helper::userCan(112, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger reject" data-id="' . $row->id . '">Reject</button>';
                    }
                    $btn .= '</div>';
                    return Helper::userAllowed(112) ? $btn : '';
                })
                ->editColumn('approve_status', function ($row) {
                    if ($row['approve_status'] == 1) {
                        return '<small class="badge fw-semi-bold rounded-pill badge-light-success">Approved</small>';
                    } elseif ($row['approve_status'] == 2) {
                        return '<small class="badge fw-semi-bold rounded-pill badge-light-danger">Rejected</small>';
                    } else {
                        return '<small class="badge fw-semi-bold rounded-pill badge-light-warning">Pending</small>';
                    }
                })
                ->editColumn('created_at', fn ($row) => $row->created_at->format('d F, Y'))
                ->rawColumns(['action', 'approve_status'])
                ->make(true);
        }

        return view('drplist.index');
    }

    public function show($id): JsonResponse
    {
        $drp = Drp::with('drpDetail')
        ->leftJoin('states','states.id','=','drps.state_id')
        ->leftJoin('cities','cities.id','=','drps.city_id')
        ->select('drps.*','states.name as state_name', 'cities.name as city_name')
        ->find($id);

        if (!$drp) {
            return response()->json([
                'status' => false,
                'message' => 'DRP not found.'
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'drp' => $drp,
                'drp_detail' => $drp->drpDetail
            ]
        ]);
    }

    public function approve($id): JsonResponse
    {
        $drp = Drp::find($id);
        if (!$drp) {
            return response()->json(['status' => false, 'message' => 'DRP not found.']);
        }

        $drp->approve_status = 1;
        $drp->save();

        return response()->json(['status' => true, 'message' => 'DRP approved successfully.']);
    }

    public function reject($id): JsonResponse
    {
        $drp = Drp::find($id);
        if (!$drp) {
            return response()->json(['status' => false, 'message' => 'DRP not found.']);
        }

        $drp->approve_status = 2;
        $drp->save();

        return response()->json(['status' => true, 'message' => 'DRP rejected successfully.']);
    }


}
