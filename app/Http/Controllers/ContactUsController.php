<?php
namespace App\Http\Controllers;

use App\Models\ContactUs;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class ContactUsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = ContactUs::select(['id', 'first_name', 'last_name', 'mobile', 'email', 'created_at']);
            return DataTables::of($data)
                ->addColumn('name', function ($row) {
                    return trim($row->first_name . ' ' . ($row->last_name ?? ''));
                })
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d-M-Y'); // Format as dd-Month-Year
                })
                ->addColumn('action', function ($row) {
                    return '<button class="btn btn-sm btn-info view_inquiry" data-id="' . $row->id . '">View</button>
                            <button class="btn btn-sm btn-danger delete_record" data-id="' . $row->id . '">Delete</button>';
                })
                ->rawColumns(['action','name','created_at'])
                ->make(true);
        }

        return view('inquiries.index');
    }

    public function show($id)
    {
        $inquiry = ContactUs::findOrFail($id);
        return view('inquiries.show', compact('inquiry'));
    }

    public function destroy($id)
    {
        $inquiry = ContactUs::findOrFail($id);
        $inquiry->delete();
        return response()->json(['success' => true]);
    }
}
