<?php
namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\MediationNotice;
use App\Models\FileCase;
use App\Models\NoticeTemplate;
use App\Models\Organization;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PreMediationNoticePdfSave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:premediation-notice-pdf-save';

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
        // ########################################################
        // Pre-Mediation Notice PDF Save - By Case Manager
        // ########################################################
        $caseData = FileCase::with('file_case_details','guarantors')
            ->leftJoin('mediation_notices', 'mediation_notices.file_case_id', '=', 'file_cases.id')
            ->where('mediation_notices.mediation_notice_type', 1)
            ->whereNull('mediation_notices.notice_copy')
            ->whereNull('mediation_notices.deleted_at')
            ->select(
                'file_cases.*',
                'mediation_notices.file_case_id',
                'mediation_notices.mediation_notice_type',
                'mediation_notices.notice_copy',
                'mediation_notices.notice_date',
                'mediation_notices.email_status',
                'mediation_notices.whatsapp_notice_status',
                'mediation_notices.sms_status',
            )
            ->limit(4)
            ->get();
     
        foreach ($caseData as $key => $value) {
            try {

                $noticetemplateData = NoticeTemplate::where('id', 11)->first();
                $noticeTemplate     = $noticetemplateData->notice_format;

                $organizationManager_signature = Organization::where('id', $value['organization_id'])->select('signature_org')->first();
                $organizationLetterHead_header = Organization::where('id', $value['organization_id'])->select('header_letterhead')->first();
                $organizationLetterHead_footer = Organization::where('id', $value['organization_id'])->select('footer_letterhead')->first();
                $now                           = now();
                
                $fileCaseId = $value->id;
                Log::info("Processing Pre-Mediation Pdf Save for FileCase ID: {$fileCaseId}");

                // #########################################################
                // ################# Send Email using SMTP #################
                if (empty($value->notice_copy)) {
                    
                    // Define your replacement values
                    $data = [
                        'BANK/ORGANISATION/CLAIMANT NAME'               => ($value->claimant_first_name ?? '') . '&nbsp;' . ($value->claimant_last_name ?? ''),
                        'BANK/ORGANISATION/CLAIMANT REGISTERED ADDRESS' => ($value->claimant_address1 ?? '') . '&nbsp;' . ($value->claimant_address2 ?? ''),

                        'CUSTOMER NAME'                                 => ($value->respondent_first_name ?? '') . '&nbsp;' . ($value->respondent_last_name ?? ''),
                        'CUSTOMER ADDRESS'                              => ($value->respondent_address1 ?? '') . '&nbsp;' . ($value->respondent_address2 ?? ''),
                        'CUSTOMER MOBILE NO'                            => $value->respondent_mobile ?? '',
                        'CUSTOMER MAIL ID'                              => $value->respondent_email ?? '',

                        'GUARANTOR ADDRESS'                             => $value->guarantors->guarantor_1_address ?? '',
                        'GUARANTOR MOBILE NO'                           => $value->guarantors->guarantor_1_mobile_no ?? '',
                        'GUARANTOR MAIL ID'                             => $value->guarantors->guarantor_1_email_id ?? '',

                        'CLAIM SIGNATORY/AUTHORISED OFFICER NAME'       => $value->file_case_details->claim_signatory_authorised_officer_name ?? '',
                        'CLAIM SIGNATORY/AUTHORISED OFFICER MOBILE NO'  => $value->file_case_details->claim_signatory_authorised_officer_mobile_no ?? '',
                        "CLAIM SIGNATORY/AUTHORISED OFFICER'S MAIL ID"  => $value->file_case_details->claim_signatory_authorised_officer_mail_id ?? '',

                        'CASE REGISTRATION NUMBER'                      => $value->case_number ?? '',
                        'LOAN NO'                                       => $value->loan_number ?? '',
                        'FORECLOSURE AMOUNT'                            => $value->file_case_details->foreclosure_amount ?? '',
                        'FORECLOSURE DATE'                              => $value->file_case_details->foreclosure_amount_date ?? '',
                        'AGREEMENT DATE'                                => $value->agreement_date ?? '',
                        'FINANCE AMOUNT'                                => $value->file_case_details->finance_amount ?? '',

                        'ARBITRATION CLAUSE NO'                         => $value->arbitration_clause_no ?? '',
                        'ARBITRATION DATE'                              => $value->arbitration_date ?? '',
                        'TENURE'                                        => $value->file_case_details->tenure ?? '',
                        'PRODUCT'                                       => $value->file_case_details->product ?? '',
                        'STAGE 1 NOTICE DATE'                           => $value->file_case_details->stage_1_notice_date ?? '',

                        'GUARANTOR 1 NAME'                              => $value->guarantors->guarantor_1_name ?? '',
                        'GUARANTOR 1 MOBILE NO'                         => $value->guarantors->guarantor_1_mobile_no ?? '',
                        'GUARANTOR 1 EMAIL ID'                          => $value->guarantors->guarantor_1_email_id ?? '',
                        'GUARANTOR 1 ADDRESS'                           => $value->guarantors->guarantor_1_address ?? '',
                        'GUARANTOR 1 FATHER NAME'                       => $value->guarantors->guarantor_1_father_name ?? '',
                           
                        'GUARANTOR 2 NAME'                              => $value->guarantors->guarantor_2_name ?? '',
                        'GUARANTOR 2 MOBILE NO'                         => $value->guarantors->guarantor_2_mobile_no ?? '',
                        'GUARANTOR 2 EMAIL ID'                          => $value->guarantors->guarantor_2_email_id ?? '',
                        'GUARANTOR 2 ADDRESS'                           => $value->guarantors->guarantor_2_address ?? '',
                        'GUARANTOR 2 FATHER NAME'                       => $value->guarantors->guarantor_2_father_name ?? '',

                        'GUARANTOR 3 NAME'                              => $value->guarantors->guarantor_3_name ?? '',
                        'GUARANTOR 3 MOBILE NO'                         => $value->guarantors->guarantor_3_mobile_no ?? '',
                        'GUARANTOR 3 EMAIL ID'                          => $value->guarantors->guarantor_3_email_id ?? '',
                        'GUARANTOR 3 ADDRESS'                           => $value->guarantors->guarantor_3_address ?? '',
                        'GUARANTOR 3 FATHER NAME'                       => $value->guarantors->guarantor_3_father_name ?? '',
                            
                        'GUARANTOR 4 NAME'                              => $value->guarantors->guarantor_4_name ?? '',
                        'GUARANTOR 4 MOBILE NO'                         => $value->guarantors->guarantor_4_mobile_no ?? '',
                        'GUARANTOR 4 EMAIL ID'                          => $value->guarantors->guarantor_4_email_id ?? '',
                        'GUARANTOR 4 ADDRESS'                           => $value->guarantors->guarantor_4_address ?? '',
                        'GUARANTOR 4 FATHER NAME'                       => $value->guarantors->guarantor_4_father_name ?? '',
                            
                        'GUARANTOR 5 NAME'                              => $value->guarantors->guarantor_5_name ?? '',
                        'GUARANTOR 5 MOBILE NO'                         => $value->guarantors->guarantor_5_mobile_no ?? '',
                        'GUARANTOR 5 EMAIL ID'                          => $value->guarantors->guarantor_5_email_id ?? '',
                        'GUARANTOR 5 ADDRESS'                           => $value->guarantors->guarantor_5_address ?? '',
                        'GUARANTOR 5 FATHER NAME'                       => $value->guarantors->guarantor_5_father_name ?? '',
                            
                        'GUARANTOR 6 NAME'                              => $value->guarantors->guarantor_6_name ?? '',
                        'GUARANTOR 6 MOBILE NO'                         => $value->guarantors->guarantor_6_mobile_no ?? '',
                        'GUARANTOR 6 EMAIL ID'                          => $value->guarantors->guarantor_6_email_id ?? '',
                        'GUARANTOR 6 ADDRESS'                           => $value->guarantors->guarantor_6_address ?? '',
                        'GUARANTOR 6 FATHER NAME'                       => $value->guarantors->guarantor_6_father_name ?? '',
                            
                        'GUARANTOR 7 NAME'                              => $value->guarantors->guarantor_7_name ?? '',
                        'GUARANTOR 7 MOBILE NO'                         => $value->guarantors->guarantor_7_mobile_no ?? '',
                        'GUARANTOR 7 EMAIL ID'                          => $value->guarantors->guarantor_7_email_id ?? '',
                        'GUARANTOR 7 ADDRESS'                           => $value->guarantors->guarantor_7_address ?? '',
                        'GUARANTOR 7 FATHER NAME'                       => $value->guarantors->guarantor_7_father_name ?? '',

                        'DATE'                                          => $value->notice_date ?? now()->format('d-m-Y'),
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


                    $finalNotice = $replaceSummernotePlaceholders($noticeTemplate, $data);

                    // Use full URLs
                    $headerImg    = url('storage/' . $organizationLetterHead_header['header_letterhead']);
                    $footerImg    = url('storage/' . $organizationLetterHead_footer['footer_letterhead']);
                    $signatureImg = url('storage/' . $organizationManager_signature['signature_org']);

                    // Append signature at the end of the notice
                    $finalNotice .= '
                        <div style="text-align: left; margin-top: 10px;">
                            <img src="' . $signatureImg . '" style="height: 80px;" alt="Signature">
                        </div>
                    ';

                    // Now wrap everything in proper HTML with real headers/footers
                    $html = '
                    <html>
                    <head>
                        <style>
                            @page {
                                size: A4;
                                margin: 6mm 12mm 12mm 12mm; /* top, right, bottom, left */
                                header: html_myHeader;
                                footer: html_myFooter;
                            }

                            body {
                                font-family: DejaVu Sans, sans-serif;
                                font-size: 12px;
                                line-height: 1.4;
                            }

                            img {
                                max-width: 100%;
                                height: auto;
                            }
                        </style>
                    </head>

                    <!-- Define actual header -->
                    <htmlpageheader name="myHeader">
                        <img src="' . $headerImg . '" alt="Header Image" />
                    </htmlpageheader>

                    <body>

                    ' . $finalNotice . '

                    </body>

                    <!-- Define actual footer -->
                    <htmlpagefooter name="myFooter">
                        <img src="' . $footerImg . '" alt="Footer Image" />
                    </htmlpagefooter>

                    </html>';

                    // 2. Generate PDF with A4 paper size
                    $pdf = PDF::loadHTML($html)->setPaper('A4', 'portrait')->setOptions(['isRemoteEnabled' => true]);

                    // Create temporary PDF file
                    $tempPdfPath = tempnam(sys_get_temp_dir(), 'pdf');
                    $pdf->save($tempPdfPath);

                    // Wrap temp file in UploadedFile so it can go through Helper::saveFile
                    $uploadedFile = new \Illuminate\Http\UploadedFile(
                        $tempPdfPath,
                        'notice_' . time() . '.pdf',
                        'application/pdf',
                        null,
                        true
                    );

                    // Save the PDF using your helper
                    $savedPath = Helper::loannosaveFile($uploadedFile, 'premediationnotices', $value->loan_number);

                    MediationNotice::where('file_case_id', $value->id)->where('mediation_notice_type', 1)->update([
                        'notice_copy'   => $savedPath,
                    ]);
                    Log::info("Pre-Mediation Pdf saved successfully for FileCase ID: {$fileCaseId}");
                }

            } catch (\Throwable $th) {
                Log::error("Error Saving Pre-Mediation Pdf for record ID {$value->id}: " . $th->getMessage());
            }
        }
    }
}
