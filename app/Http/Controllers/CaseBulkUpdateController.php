<?php

namespace App\Http\Controllers;

use App\Exports\SampleBulkUpdateCaseExport;
use App\Http\Controllers\Controller;
use App\Imports\BulkUpdateCaseImport;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Log;

class CaseBulkUpdateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function casebulkupdate(Request $request): View
    {
        $title = 'Bulk Update Cases';

        return view('cases.casebulkupdate', compact('title'));
    }

    public function importBulkUpdateExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx,csv|max:2048', 
        ]);

        try {
            Log::info('Import function triggered, file received.');
            Excel::import(new BulkUpdateCaseImport(), $request->file('file'));
            Log::info('Import process completed.');
            
            return redirect()->route('cases.casebulkupdate')->with('success', 'Bulk Update File imported successfully.');

        } catch (\Exception $e) {
            Log::error('File import failed: ' . $e->getMessage());
            return back()->with('error', 'File import failed. Please check the format.');
        }
    }

    public function downloadBulkUpdateSample()
    {
        return Excel::download(new SampleBulkUpdateCaseExport, 'sample_file_case_bulk_update.xlsx');
    }

}