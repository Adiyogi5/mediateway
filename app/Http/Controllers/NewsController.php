<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\News;
use Illuminate\View\View;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class NewsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = News::select('id', 'title', 'image', 'status', 'created_at');
            return Datatables::of($data)
                ->editColumn('image', function ($row) {
                    $btn = '<div class="img-group"><img class="" src="' . asset('storage/' . $row['image']) . '" alt=""></div>';
                    return $btn;
                })
                ->editColumn('created_at', function ($row) {
                    return $row['created_at']->format('d M, Y');
                })
                ->editColumn('status', function ($row) {
                    return $row['status'] == 1 ? '<small class="badge fw-semi-bold rounded-pill status badge-light-success"> Active</small>' : '<small class="badge fw-semi-bold rounded-pill status badge-light-danger"> Inactive</small>';
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (Helper::userCan(104, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('news.edit', $row['id']) . '">Edit</a>';
                    }
                    if (Helper::userCan(111, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }
                    if (Helper::userAllowed(104)) {
                        return $btn;
                    } else {
                        return '';
                    }
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'image', 'status'])
                ->make(true);
        }
        return view('news.index');
    }

    public function add(): View
    {
        return view('news.add');
    }

    public function save(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'             => ['required', 'string', 'max:200'],
            'post_by'           => ['required'],
            'date'              => ['required'],
            'short_description' => ['required', 'string', 'max:300'],
            'description'       => ['required', 'string', 'max:10000'],
            'status'            => ['required', 'integer'],
            'image'             => ['image', 'mimes:jpg,png,jpeg', 'max:5048']
        ]);

        $data = [...$validated, 'image' => 'news/image.png'];
        if ($request->file('image')) {
            $data['image'] = Helper::saveFile($request->file('image'), 'news');
        }

        News::create($data);
        return to_route('news')->withSuccess('News Added Successfully..!!');
    }

    public function edit($id): View|RedirectResponse
    {
        $news = News::find($id);
        if (!$news) {
            return to_route('news')->withError('News Not Found..!!');
        }
        return view('news.edit', compact('news'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $news = News::find($id);
        if (!$news) {
            return to_route('news')->withError('News Not Found..!!');
        }

        $data = $request->validate([
            'title'             => ['required', 'string', 'max:200'],
            'post_by'           => ['required'],
            'date'              => ['required'],
            'short_description' => ['required', 'string', 'max:300'],
            'description'       => ['required', 'string', 'max:10000'],
            'status'            => ['required', 'integer'],
            'image'             => ['image', 'mimes:jpg,png,jpeg', 'max:5048']
        ]);

        if ($request->file('image')) {
            Helper::deleteFile($news->image);
            $data['image'] = Helper::saveFile($request->file('image'), 'news');
        }

        $news->update($data);
        return to_route('news')->withSuccess('News Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new News, $request->id);
    }
}
