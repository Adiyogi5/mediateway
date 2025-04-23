<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use App\Models\NoticeTemplate;
use App\Models\NoticeTemplateVariable;
use Illuminate\View\View;
use Illuminate\Support\Str;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NoticeTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
{
    if ($request->ajax()) {
        $data = NoticeTemplate::select('id', 'name', 'status', 'created_at');

        return Datatables::of($data)
            ->editColumn('status', function ($row) {
                return $row['status'] == 1
                    ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>'
                    : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
            })
            ->editColumn('created_at', function ($row) {
                return $row['created_at']->format('d M, Y');
            })
            ->addColumn('action', function ($row) {
                $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                
                if (Helper::userCan(111, 'can_edit')) {
                    $btn .= '<a class="dropdown-item" href="' . route('noticetemplate.edit', $row['id']) . '">Edit</a>';
                }
                if (Helper::userCan(111, 'can_delete')) {
                    $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                }

                return Helper::userAllowed(111) ? $btn : '';
            })
            ->orderColumn('created_at', function ($query, $order) {
                $query->orderBy('created_at', $order);
            })
            ->rawColumns(['action', 'status'])
            ->make(true);
    }

    $title = "Notice Template";
    return view('noticetemplate.index', compact('title'));
}


public function add(): View
{
    return view('noticetemplate.add');
}

public function save(Request $request): RedirectResponse
{
    $validated = $request->validate([
        'name'           => ['required', 'string', 'unique:notice_templates,name', 'max:100'],
        'subject'        => ['required', 'string'],
        'email_content'  => ['required', 'string'],
        'notice_format'  => ['required', 'string']
    ]);

    NoticeTemplate::create($validated + ['status' => 1]);
    
    return to_route('noticetemplate')->withSuccess('Notice Template Added Successfully..!!');
}

public function edit($id): View|RedirectResponse
{
    $noticetemplate = NoticeTemplate::find($id);
    if (!$noticetemplate) return to_route('noticetemplate')->withError('Notice Template Not Found..!!');

    return view('noticetemplate.edit', compact('noticetemplate'));
}

public function update(Request $request, $id): RedirectResponse
{
    $noticetemplate = NoticeTemplate::find($id);
    if (!$noticetemplate) return to_route('noticetemplate')->withError('Notice Template Not Found..!!');

    $validated = $request->validate([
        'name'           => ['required', 'string', 'unique:notice_templates,name,' . $id, 'max:100'],
        'subject'        => ['required', 'string'],
        'email_content'  => ['required', 'string'],
        'notice_format'  => ['required', 'string']
    ]);

    $noticetemplate->update($validated);
    
    return to_route('noticetemplate')->withSuccess('Notice Template Updated Successfully..!!');
}

public function delete(Request $request): JsonResponse
{
    return Helper::deleteRecord(new NoticeTemplate, $request->id);
}

public function getnoticetemplateVariables(): JsonResponse
{
    return response()->json(NoticeTemplateVariable::all());
}

}
