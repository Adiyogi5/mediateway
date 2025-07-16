<?php
namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\ConciliationNotice;
use App\Models\FileCase;
use App\Models\NoticeTemplate;
use App\Models\Organization;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PreConciliationNoticePdfSave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:preconciliation-notice-pdf-save';

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
        // Pre-Conciliation Notice PDF Save - By Case Manager
        // ########################################################
        $caseData = FileCase::with('file_case_details','guarantors')
            ->leftJoin('conciliation_notices', 'conciliation_notices.file_case_id', '=', 'file_cases.id')
            ->where('conciliation_notices.conciliation_notice_type', 1)
            ->whereNull('conciliation_notices.notice_copy')
            ->select(
                'file_cases.*',
                'conciliation_notices.file_case_id',
                'conciliation_notices.conciliation_notice_type',
                'conciliation_notices.notice_copy',
                'conciliation_notices.email_status'
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
                Log::info("Processing Pre-Conciliation Pdf Save for FileCase ID: {$fileCaseId}");

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
                        'TENURE'                                        => $value->file_case_details->tenure ?? '',

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
                    $savedPath = Helper::loannosaveFile($uploadedFile, 'preconciliationnotices', $value->loan_number);

                    ConciliationNotice::where('file_case_id', $value->id)->where('conciliation_notice_type', 1)->update([
                        'notice_copy'   => $savedPath,
                    ]);
                    Log::info("Pre-Conciliation Pdf saved successfully for FileCase ID: {$fileCaseId}");
                }

            } catch (\Throwable $th) {
                Log::error("Error Saving Pre-Conciliation Pdf for record ID {$value->id}: " . $th->getMessage());
            }
        }
    }
}
