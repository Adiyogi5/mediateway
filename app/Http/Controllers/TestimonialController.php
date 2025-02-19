<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use Illuminate\View\View;
use App\Models\Testimonial;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use \Yajra\Datatables\Datatables;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class TestimonialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $data = Testimonial::select('id', 'name', 'description', 'image', 'status', 'created_at');

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
                ->editColumn('description', function ($row) {
                    return Str::limit($row['description'], 40);
                })
                ->addColumn('action', function ($row) {

                    $btn = '<button class="text-600 btn-reveal dropdown-toggle btn btn-link btn-sm" id="drop" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="fas fa-ellipsis-h fs--1"></span></button><div class="dropdown-menu" aria-labelledby="drop">';
                    if (Helper::userCan(111, 'can_edit')) {
                        $btn .= '<a class="dropdown-item" href="' . route('testimonials.edit', $row['id']) . '">Edit</a>';
                    }
                    if (Helper::userCan(111, 'can_delete')) {
                        $btn .= '<button class="dropdown-item text-danger delete" data-id="' . $row['id'] . '">Delete</button>';
                    }

                    return Helper::userAllowed(111) ? $btn : '';
                })
                ->orderColumn('created_at', function ($query, $order) {
                    $query->orderBy('created_at', $order);
                })
                ->rawColumns(['action', 'image', 'status'])
                ->make(true);
        }
        return view('testimonials.index');
    }

    public function add(): View
    {
        return view('testimonials.add');
    }

    public function save(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:50'],
            'rating'        => ['required'],
            'status'        => ['required', 'integer'],
            'description'   => ['required', 'string', 'max:500'],
            'image'         => ['image', 'mimes:jpg,png,jpeg', 'max:2048']
        ]);

        $data = [...$validated, 'image' => 'admin/avatar.png'];
        if ($request->hasFile('image')) {
            $data['image']        = Helper::saveFile($request->file('image'), 'testimonials');
        }

        Testimonial::create($data);
        return to_route('testimonials')->withSuccess('Testimonial Added Successfully..!!');
    }

    public function edit($id): View|RedirectResponse
    {
        $testimonial = Testimonial::find($id);
        if (!$testimonial) return to_route('testimonials')->withError('Testimonial Not Found..!!');

        return view('testimonials.edit', compact('testimonial'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $testimonial =  Testimonial::find($id);
        if (!$testimonial) return to_route('testimonials')->withError('Testimonial Not Found..!!');

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:50'],
            'rating'        => ['required'],
            'status'        => ['required', 'integer'],
            'description'   => ['required', 'string', 'max:500'],
            'image'         => ['image', 'mimes:jpg,png,jpeg', 'max:2048']
        ]);

        if ($request->hasFile('image')) {
            Helper::deleteFile($testimonial->image);
            $data['image']        = Helper::saveFile($request->file('image'), 'testimonials');
        }

        $testimonial->update($data);
        return to_route('testimonials')->withSuccess('Testimonial Updated Successfully..!!');
    }

    public function delete(Request $request): JsonResponse
    {
        return Helper::deleteRecord(new Testimonial, $request->id);
    }
}
