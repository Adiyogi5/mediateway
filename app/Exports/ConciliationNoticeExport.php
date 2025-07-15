<?php

namespace App\Exports;

use App\Models\ConciliationNotice;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ConciliationNoticeExport implements FromView
{
    protected $request;
    protected $master_id;

    public function __construct($request, $master_id)
    {
        $this->request = $request;
        $this->master_id = $master_id;
    }

    public function view(): View
    {
        $drp = auth('drp')->user();

        $data = ConciliationNotice::select(
                'conciliation_notices.*',
                'file_cases.case_type',
                'file_cases.product_type',
                'file_cases.case_number',
                'file_cases.loan_number',
                'file_cases.claimant_first_name',
                'file_cases.respondent_email',
                'file_cases.respondent_mobile',
                'file_cases.status',
                'file_cases.created_at',
                'assign_cases.case_manager_id'
            )
            ->join('file_cases', 'file_cases.id', '=', 'conciliation_notices.file_case_id')
            ->join('assign_cases', 'assign_cases.case_id', '=', 'conciliation_notices.file_case_id')
            ->where('conciliation_notices.conciliation_master_id', $this->master_id)
            ->where('assign_cases.case_manager_id', $drp->id);

        // Apply filters
        if ($this->request->filled('case_type')) {
            $data->where('file_cases.case_type', $this->request->case_type);
        }
        if ($this->request->filled('product_type')) {
            $data->where('file_cases.product_type', $this->request->product_type);
        }
        if ($this->request->filled('conciliation_notice_type')) {
            $data->where('conciliation_notices.conciliation_notice_type', $this->request->conciliation_notice_type);
        }
        if ($this->request->filled('case_number')) {
            $data->where('file_cases.case_number', 'like', '%' . $this->request->case_number . '%');
        }
        if ($this->request->filled('loan_number')) {
            $data->where('file_cases.loan_number', 'like', '%' . $this->request->loan_number . '%');
        }
        if ($this->request->filled('status')) {
            $data->where('file_cases.status', $this->request->status);
        }
        if ($this->request->filled('date_from') && $this->request->filled('date_to')) {
            $data->whereBetween('conciliation_notices.notice_date', [
                $this->request->date_from . ' 00:00:00',
                $this->request->date_to . ' 23:59:59'
            ]);
        }

        return view('exports.conciliation_notices', ['data' => $data->get()]);
    }
}
