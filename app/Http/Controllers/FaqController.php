<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Helper\Helper;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;

class FaqController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = Faq::select('id', 'question', 'answer', 'status');
            return Datatables::of($data)
                ->addColumn('action', function ($row) {
                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (Helper::userCan(110, 'can_edit')) {
                        $btn .= '<button class="dropdown-item edit" data-all="' . htmlspecialchars(json_encode($row))  . '">Edit</button>';
                    }
                    if (Helper::userCan(110, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }

                    return Helper::userAllowed(110) ? $btn : '';
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->editColumn('question', fn ($row) => Str::limit($row['question'], 50))
                ->editColumn('answer', fn ($row) => Str::limit($row['answer'], 50))
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        return view('faqs.index');
    }

    public function save(Request $request): JsonResponse
    {
        return Helper::checkValid([
            'question'  => ['required', 'string', 'max:200'],
            'answer'    => ['required', 'string', 'max:200'],
            'status'    => ['required', 'integer'],
        ], function ($validator) {
            Faq::create($validator->validated());
            return response()->json([
                'status'    => true,
                'message'   => 'Faq Added Successfully',
                'data'      => ''
            ]);
        });
    }

    public function update(Request $request): JsonResponse
    {
        $state = Faq::find($request->id);
        if (!$state)  return response()->json([
            'status'    => false,
            'message'   => 'Faq Not Found..!!',
        ]);

        return Helper::checkValid([
            'id'        => ['required'],
            'question'  => ['required', 'string', 'max:200'],
            'answer'    => ['required', 'string', 'max:200'],
            'status'    => ['required', 'integer'],
        ], function ($validator) use ($state) {
            $state->update($validator->validated());
            return response()->json([
                'status'    => true,
                'message'   => 'Faq Added Successfully',
            ]);
        });
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new Faq, $request->id);
    }
}
