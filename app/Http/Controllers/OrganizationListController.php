<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\OrganizationList;
use App\Models\OrganizationNoticeTimeline;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use \Yajra\Datatables\Datatables;

class OrganizationListController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            // $data = OrganizationList::query();
            $data = OrganizationList::with('noticeTimeline')->select('*');

            return Datatables::of($data)
                ->addColumn('action', function ($row) {
                    $allData = $row->toArray();
                    $allData['notice_1'] = $row->noticeTimeline->notice_1 ?? '';
                    $allData['notice_1a'] = $row->noticeTimeline->notice_1a ?? '';
                    $allData['notice_1b'] = $row->noticeTimeline->notice_1b ?? '';
                    $allData['notice_2b'] = $row->noticeTimeline->notice_2b ?? '';
                    $allData['notice_3a'] = $row->noticeTimeline->notice_3a ?? '';
                    $allData['notice_3b'] = $row->noticeTimeline->notice_3b ?? '';
                    $allData['notice_3c'] = $row->noticeTimeline->notice_3c ?? '';
                    $allData['notice_3d'] = $row->noticeTimeline->notice_3d ?? '';
                    $allData['notice_4a'] = $row->noticeTimeline->notice_4a ?? '';
                    $allData['notice_5a'] = $row->noticeTimeline->notice_5a ?? '';
                    $allData['notice_second_hearing'] = $row->noticeTimeline->notice_second_hearing ?? '';
                    $allData['notice_final_hearing'] = $row->noticeTimeline->notice_final_hearing ?? '';
                
                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                
                    if (Helper::userCan(112, 'can_edit')) {
                        $btn .= '<button class="dropdown-item edit" data-all="' . htmlspecialchars(json_encode($allData)) . '">Edit</button>';
                    }
                    if (Helper::userCan(112, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row->id . '">Delete</button>';
                    }
                    $btn .= '</div>';
                    return Helper::userAllowed(112) ? $btn : '';
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1
                        ? '<small class="badge fw-semi-bold rounded-pill badge-light-success">Active</small>'
                        : '<small class="badge fw-semi-bold rounded-pill badge-light-danger">Inactive</small>';
                })
                ->editColumn('created_at', fn ($row) => $row->created_at->format('d F, Y'))
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('organizationlist.index');
    }

    public function save(Request $request): JsonResponse
    {
        return Helper::checkValid([
            'name'   => ['required'],
            'code'   => ['required'],
            'status' => ['required', 'integer'],
             // Add all notice fields as required
            'notice_1' => ['required'],
            'notice_1a' => ['required'],
            'notice_1b' => ['required'],
            'notice_2b' => ['required'],
            'notice_3a' => ['required'],
            'notice_3b' => ['required'],
            'notice_3c' => ['required'],
            'notice_3d' => ['required'],
            'notice_4a' => ['required'],
            'notice_5a' => ['required'],
            'notice_second_hearing' => ['required'],
            'notice_final_hearing' => ['required'],
        ], function ($validator) use($request) {
            $data = $validator->validated();
            $organization = OrganizationList::create($data);

            // Save organization_notice_timelines
            OrganizationNoticeTimeline::create([
                'organization_list_id' => $organization->id,
                'notice_1' => $request->notice_1,
                'notice_1a' => $request->notice_1a,
                'notice_1b' => $request->notice_1b,
                'notice_2b' => $request->notice_2b,
                'notice_3a' => $request->notice_3a,
                'notice_3b' => $request->notice_3b,
                'notice_3c' => $request->notice_3c,
                'notice_3d' => $request->notice_3d,
                'notice_4a' => $request->notice_4a,
                'notice_5a' => $request->notice_5a,
                'notice_second_hearing' => $request->notice_second_hearing,
                'notice_final_hearing' => $request->notice_final_hearing,
            ]);

            return response()->json([
                'status'  => true,
                'message' => 'Organization added successfully.',
                'data'    => '',
            ]);
        });
    }

    public function update(Request $request): JsonResponse
    {
        $organizationlist = OrganizationList::find($request->id);
        if (!$organizationlist) {
            return response()->json([
                'status'  => false,
                'message' => 'Organization not found!',
                'data'    => '',
            ]);
        }

        return Helper::checkValid([
            'name'   => ['required'],
            'code'   => ['required'],
            'status' => ['required', 'integer'],
            // Add all notice fields as required
           'notice_1' => ['required'],
           'notice_1a' => ['required'],
           'notice_1b' => ['required'],
           'notice_2b' => ['required'],
           'notice_3a' => ['required'],
           'notice_3b' => ['required'],
           'notice_3c' => ['required'],
           'notice_3d' => ['required'],
           'notice_4a' => ['required'],
           'notice_5a' => ['required'],
           'notice_second_hearing' => ['required'],
           'notice_final_hearing' => ['required'],
        ], function ($validator) use ($organizationlist, $request) {
            $data = $validator->validated();
            $organizationlist->update($data);

            // Find the existing notice timeline or create a new one if it doesn't exist
            $timeline = OrganizationNoticeTimeline::firstOrNew([
                'organization_list_id' => $organizationlist->id
            ]);

            $timeline->fill([
                'notice_1' => $request->notice_1,
                'notice_1a' => $request->notice_1a,
                'notice_1b' => $request->notice_1b,
                'notice_2b' => $request->notice_2b,
                'notice_3a' => $request->notice_3a,
                'notice_3b' => $request->notice_3b,
                'notice_3c' => $request->notice_3c,
                'notice_3d' => $request->notice_3d,
                'notice_4a' => $request->notice_4a,
                'notice_5a' => $request->notice_5a,
                'notice_second_hearing' => $request->notice_second_hearing,
                'notice_final_hearing' => $request->notice_final_hearing,
            ])->save();

            return response()->json([
                'status'  => true,
                'message' => 'Organization updated successfully.',
                'data'    => '',
            ]);
        });
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new OrganizationList, $request->id);
    }
}
