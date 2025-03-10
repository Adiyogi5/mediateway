<?php

namespace App\Http\Controllers\Organization;

use App\Helper\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\FileCaseImport;
use App\Exports\SampleFileCaseExport;

class FileCaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:organization');
    }

    public function index(Request $request): View | RedirectResponse
    {
        $title = 'File Cases';

        $organization = auth('organization')->user();

        if (!$organization) {
            return to_route('front.home')->withInfo('Please enter your valid details.');
        }

        return view('organization.filecase', compact('organization','title'));
    }

        public function importExcel(Request $request)
        {
            $request->validate([
                'file' => 'required|mimes:xlsx,csv',
            ]);

            $organizationId = auth()->user()->id ?? null; // Adjust based on your logic

            if (!$organizationId) {
                return back()->with('error', 'No organization found.');
            }
    
            Excel::import(new FileCaseImport($organizationId), $request->file('file'));
    
            return back()->with('success', 'File imported successfully.');
        }


    public function downloadSample()
    {
        return Excel::download(new SampleFileCaseExport, 'sample_file_case.xlsx');
    }

}