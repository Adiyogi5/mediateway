<?php
namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\AssignCase;
use App\Models\ClaimPetition;
use App\Models\Drp;
use App\Models\FileCase;
use App\Models\FileCaseDetail;
use App\Models\Notice;
use App\Models\Organization;
use App\Models\OrganizationList;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class CreateClaimPetition extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:create-claim-petition';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // ##############################################
        // Create Claim Petition - 3D - Save Pdf
        // ##############################################
        $caseData = FileCase::with('file_case_details','guarantors')
            ->join(DB::raw("(
                SELECT
                    id AS org_id,
                    name AS org_name,
                    IF(parent_id = 0, id, parent_id) AS effective_parent_id,
                    IF(parent_id = 0, name,
                        (SELECT name FROM organizations AS parent_org WHERE parent_org.id = organizations.parent_id)
                    ) AS effective_parent_name
                FROM organizations
            ) AS org_with_parent"), 'org_with_parent.org_id', '=', 'file_cases.organization_id')
            ->join('organization_lists', 'org_with_parent.effective_parent_name', '=', 'organization_lists.name')
            ->join('organization_notice_timelines', 'organization_notice_timelines.organization_list_id', '=', 'organization_lists.id')
            ->whereHas('notices', function ($query) {
                $query->where('notice_type', 1)
                    ->whereRaw('DATEDIFF(CURDATE(), DATE(CONVERT_TZ(notices.notice_date, "+00:00", "-06:00"))) >= organization_notice_timelines.notice_3d');
            })
            ->where('file_cases.claim_petition', NULL)
            ->whereIn('organization_notice_timelines.notice_3d', function ($query) {
                $query->select('notice_3d')
                    ->from('organization_notice_timelines')
                    ->whereNull('deleted_at')
                    ->whereRaw('organization_notice_timelines.organization_list_id = organization_lists.id');
            })
            ->select(
                'file_cases.*',
                'organization_notice_timelines.notice_3d',
                DB::raw('org_with_parent.effective_parent_id as parent_id'),
                DB::raw('org_with_parent.effective_parent_name as parent_name')
            )
            ->distinct()
            ->get();
                
        foreach ($caseData as $key => $value) {
            try {
                $assigncaseData = AssignCase::where('case_id', $value->id)->first();
                // $noticedataFetchCaseManager = Notice::where('file_case_id', $value->id)->where('notice_type', 8)->first();

                if (!empty($assigncaseData)) {
                    $arbitratorIds   = explode(',', $assigncaseData->arbitrator_id);
                    $arbitratorsName = Drp::whereIn('id', $arbitratorIds)->pluck('name')->implode(', ');
                    $casemanagerData = Drp::where('id', $assigncaseData->case_manager_id)->first();

                    $organizationManager_signature = Organization::where('id', $value['organization_id'])->select('signature_org')->first();


                    $claimpetitionData = ClaimPetition::where('product_type', $value->product_type)->first();
                    $claimPetition     = $claimpetitionData->notice_format;

                    // Define your replacement values
                    $data = [
                        "ARBITRATOR'S NAME"                                               => $arbitratorsName ?? '',
                        "CASE MANAGER'S NAME"                                             => $casemanagerData->name ?? '',
                        "CASE MANAGER'S PHONE NUMBER"                                     => $casemanagerData->mobile ?? '',
                        "CASE MANAGER'S EMAIL ADDRESS"                                    => ($casemanagerData->address1 ?? '') . '&nbsp;' . ($casemanagerData->address2 ?? ''),

                        'CASE REGISTRATION NUMBER'                                        => $value->case_number ?? '',
                        'BANK/ORGANISATION/CLAIMANT NAME'                                 => ($value->claimant_first_name ?? '') . '&nbsp;' . ($value->claimant_last_name ?? ''),
                        'BANK/ORGANISATION/CLAIMANT REGISTERED ADDRESS'                   => ($value->claimant_address1 ?? '') . '&nbsp;' . ($value->claimant_address2 ?? ''),

                        'CLAIM SIGNATORY/AUTHORISED OFFICER NAME'                         => $value->file_case_details->claim_signatory_authorised_officer_name ?? '',
                        'CLAIM SIGNATORY/AUTHORISED OFFICER MOBILE NO'                    => $value->file_case_details->claim_signatory_authorised_officer_mobile_no ?? '',
                        "CLAIM SIGNATORY/AUTHORISED OFFICER'S MAIL ID"                    => $value->file_case_details->claim_signatory_authorised_officer_mail_id ?? '',

                        'ASSET DESCRIPTION'                                               => $value->file_case_details->asset_description ?? '',
                        'REGISTRATION NO'                                                 => $value->file_case_details->registration_no ?? '',

                        'LOAN NUMBER'                                                     => $value->loan_number ?? '',
                        'LOAN APPLICATION DATE'                                           => $value->loan_application_date ?? '',
                        'AGREEMENT DATE'                                                  => $value->agreement_date ?? '',
                        'FINANCE AMOUNT'                                                  => $value->file_case_details->finance_amount ?? '',
                        'FINANCE AMOUNT IN WORDS'                                         => $value->file_case_details->finance_amount_in_words ?? '',
                        'TENURE'                                                          => $value->file_case_details->tenure ?? '',
                        'RATE OF INTEREST'                                                => $value->file_case_details->rate_of_interest ?? '',
                        'EMI DUE DATE'                                                    => $value->file_case_details->emi_due_date ?? '',
                        'FORECLOSURE AMOUNT'                                              => $value->file_case_details->foreclosure_amount ?? '',
                        'FORECLOSURE AMOUNT IN WORDS'                                     => $value->file_case_details->foreclosure_amount_in_words ?? '',
                        'FORECLOSURE AMOUNT DATE'                                         => $value->file_case_details->foreclosure_amount_date ?? '',

                        "ARBITRATOR'S NAME"                                               => $arbitratorsData->name ?? '',
                        "ARBITRATOR'S SPECIALIZATION"                                     => $arbitratorsData->specialization ?? '',
                        "ARBITRATOR'S ADDRESS"                                            => ($arbitratorsData->address1 ?? '') . '&nbsp;' . ($arbitratorsData->address2 ?? ''),

                        'CUSTOMER NAME'                                                   => ($value->respondent_first_name ?? '') . '&nbsp;' . ($value->respondent_last_name ?? ''),
                        'CUSTOMER FATHER NAME'                                            => ($value->respondent_first_name ?? '') . '&nbsp;' . ($value->respondent_last_name ?? ''),
                        'CUSTOMER ADDRESS'                                                => ($value->respondent_address1 ?? '') . '&nbsp;' . ($value->respondent_address2 ?? ''),
                        'CUSTOMER MOBILE NO'                                              => $value->respondent_mobile ?? '',
                        'CUSTOMER MAIL ID'                                                => $value->respondent_email ?? '',

                        'ARBITRATION CLAUSE NO'                                           => $value->arbitration_clause_no ?? '',

                        'GUARANTOR 1 NAME'                                                => $value->guarantors->guarantor_1_name ?? '',
                        'GUARANTOR 1 ADDRESS'                                             => $value->guarantors->guarantor_1_address ?? '',
                        'GUARANTOR 1 MOBILE NO'                                           => $value->guarantors->guarantor_1_mobile_no ?? '',
                        'GUARANTOR 1 MAIL ID'                                             => $value->guarantors->guarantor_1_email_id ?? '',

                        'GUARANTOR 2 NAME'                                                => $value->guarantors->guarantor_2_name ?? '',
                        'GUARANTOR 2 ADDRESS'                                             => $value->guarantors->guarantor_2_address ?? '',
                        'GUARANTOR 2 MOBILE NO'                                           => $value->guarantors->guarantor_2_mobile_no ?? '',
                        'GUARANTOR 2 MAIL ID'                                             => $value->guarantors->guarantor_2_email_id ?? '',

                        'GUARANTOR 3 NAME'                                                => $value->guarantors->guarantor_3_name ?? '',
                        'GUARANTOR 3 ADDRESS'                                             => $value->guarantors->guarantor_3_address ?? '',
                        'GUARANTOR 3 MOBILE NO'                                           => $value->guarantors->guarantor_3_mobile_no ?? '',
                        'GUARANTOR 3 MAIL ID'                                             => $value->guarantors->guarantor_3_email_id ?? '',

                        'DATE'                                                            => now()->format('d-m-Y'),
                        'STAGE 1 NOTICE DATE'                                             => $value->file_case_details->stage_1_notice_date ?? '',
                        'STAGE 2B NOTICE DATE'                                            => $value->file_case_details->stage_2b_notice_date ?? '',
                        'STAGE 3A NOTICE DATE'                                            => $value->file_case_details->stage_3a_notice_date ?? '',
                        'STAGE 3B NOTICE DATE'                                            => $value->file_case_details->stage_3b_notice_date ?? '',
                        'STAGE 3C NOTICE DATE'                                            => $value->file_case_details->stage_3c_notice_date ?? '',
                        'STAGE 3D NOTICE DATE'                                            => now()->format('d-m-Y'),
                    ];
                 
                    $replaceSummernotePlaceholders = function ($html, $replacements) {
                        foreach ($replacements as $key => $value) {
                            // Escape key for regex
                            $escapedKey = preg_quote($key, '/');

                            // Split into words
                            $words = preg_split('/\s+/', $escapedKey);

                            // Allow tags or spacing between words
                            $pattern = '/\{\{(?:\s|&nbsp;|<[^>]+>)*' . implode('(?:\s|&nbsp;|<[^>]+>)*', $words) . '(?:\s|&nbsp;|<[^>]+>)*\}\}/iu';

                            // Replace using callback
                            $html = preg_replace_callback($pattern, function () use ($value) {
                                return $value;
                            }, $html);
                        }

                        return $html;
                    };

                    $finalNotice = $replaceSummernotePlaceholders($claimPetition, $data);
                  
                    // Append the signature image at the end of the content, aligned right
                    $finalNotice .= '
                        <div style="text-align: right; margin-top: 0px;">
                            <img src="' . asset('storage/' . $organizationManager_signature['signature_org']) . '" style="height: 80px;" alt="Signature">
                        </div>
                    ';

                    // 1. Prepare your HTML with custom styles
                    $html = '
                    <style>
                        @page {
                            size: A4;
                            margin: 12mm;
                        }
                        body {
                            font-family: DejaVu Sans, sans-serif;
                            font-size: 12px;
                            line-height: 1.4;
                        }
                        p {
                            margin: 0px 0;
                            padding: 0;
                        }
                        img {
                            max-width: 100%;
                            height: auto;
                        }
                    </style>
                    ' . $finalNotice;

                    // 2. Generate PDF with A4 paper size
                    $pdf = PDF::loadHTML($html)->setPaper('A4', 'portrait')->setOptions(['isRemoteEnabled' => true]);
                  
                    // Create temporary PDF file
                    $tempPdfPath = tempnam(sys_get_temp_dir(), 'pdf');
                    $pdf->save($tempPdfPath);

                    // Wrap temp file in UploadedFile so it can go through Helper::saveFile
                    $uploadedFile = new \Illuminate\Http\UploadedFile(
                        $tempPdfPath,
                        'claimpetition_' . time() . '.pdf',
                        'application/pdf',
                        null,
                        true
                    );

                    // Save the PDF using your helper
                    $savedPath = Helper::saveFile($uploadedFile, 'notices');
                    
                    $now = now();
             
                    $notice = Notice::create([
                        'file_case_id'               => $value->id,
                        'notice_type'                => 8,
                        'notice'                     => $savedPath,
                        'notice_date'                => now(),
                        'notice_send_date'           => null,
                        'email_status'               => 0,
                        'whatsapp_status'            => 0,
                        'whatsapp_notice_status'     => 0,
                        'whatsapp_dispatch_datetime' => null,
                    ]);

                    if ($notice) {
                        FileCaseDetail::where('file_case_id', $notice->file_case_id)
                            ->update([
                                'stage_3d_notice_date' => $now->format('Y-m-d'),
                            ]);
                    }

                    // First Hearing Date-- And -- Second Hearing Date--
                    $casefirstnotice = Notice::where('file_case_id',$value->id)->where('notice_type',1)->first();
                    $secondhearingtimeleine = OrganizationList::with('noticeTimeline')->where('name',$value->parent_name)->first();
                    $firsthearingdate = Carbon::parse($casefirstnotice->notice_date)->addDays($secondhearingtimeleine->noticeTimeline->notice_5a);
                    $secondhearingdate = Carbon::parse($casefirstnotice->notice_date)->addDays($secondhearingtimeleine->noticeTimeline->notice_second_hearing);
        
                    // Update the existing Notice record with the claim petition path
                    FileCase::where('id', $value->id)->update([
                        'first_hearing_date'  => $firsthearingdate,
                        'second_hearing_date' => $secondhearingdate,
                        'claim_petition'      => $savedPath,
                    ]);
                }
            } catch (\Throwable $th) {
                Log::error("Error save claim petition for record ID {$value->id}: " . $th->getMessage());
            }
        }
    }
}
