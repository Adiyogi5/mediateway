<?php
namespace App\Console\Commands;

use App\Helper\Helper;
use App\Models\AssignCase;
use App\Models\Drp;
use App\Models\FileCase;
use App\Models\FileCaseDetail;
use App\Models\Notice;
use App\Models\NoticeTemplate;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class Bulk4ANoticeEmailSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:send-email-4a-notice';

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
        // ####################################################
        // Stage 4-A Notice: by Arbitrator through Case Manager
        // ####################################################
        // $caseData = FileCase::with('file_case_details')
        //     ->join(DB::raw("(
        //         SELECT
        //             id AS org_id,
        //             name AS org_name,
        //             IF(parent_id = 0, id, parent_id) AS effective_parent_id,
        //             IF(parent_id = 0, name,
        //                 (SELECT name FROM organizations AS parent_org WHERE parent_org.id = organizations.parent_id)
        //             ) AS effective_parent_name
        //         FROM organizations
        //     ) AS org_with_parent"), 'org_with_parent.org_id', '=', 'file_cases.organization_id')
        //     ->join('organization_lists', 'org_with_parent.effective_parent_name', '=', 'organization_lists.name')
        //     ->join('organization_notice_timelines', 'organization_notice_timelines.organization_list_id', '=', 'organization_lists.id')
        //     ->join('notices', 'notices.file_case_id', '=', 'file_cases.id')
        //     ->whereHas('notices', function ($query) {
        //         $query->where('notice_type', 1)
        //             ->whereRaw('DATEDIFF(CURDATE(), DATE(CONVERT_TZ(notices.notice_date, "+00:00", "-06:00"))) >= organization_notice_timelines.notice_4a');
        //     })
        //     ->where(function ($query) {
        //         $query->WhereHas('notices', function ($q) {
        //             $q->where('notice_type', 9)
        //                 ->where(function ($inner) {
        //                     $inner->where('email_status', 0)
        //                            ->orWhere('whatsapp_notice_status', 0)
        //                            ->orWhere('sms_status', 0);
        //                 });
        //         });
        //     })
        //     ->whereIn('organization_notice_timelines.notice_4a', function ($query) {
        //         $query->select('notice_4a')
        //             ->from('organization_notice_timelines')
        //             ->whereNull('deleted_at')
        //             ->whereRaw('organization_notice_timelines.organization_list_id = organization_lists.id');
        //     })
        //     ->where('notices.notice_type', 9)
        //     ->select(
        //         'file_cases.*', 'notices.notice', 'notices.email_status', 'notices.whatsapp_notice_status', 'notices.sms_status',
        //         'organization_notice_timelines.notice_4a',
        //         DB::raw('org_with_parent.effective_parent_id as parent_id'),
        //         DB::raw('org_with_parent.effective_parent_name as parent_name')
        //     )
        //     ->distinct()
        //     ->limit(20)
        //     ->get();

        $caseData = FileCase::with('file_case_details', 'guarantors')
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

        // Join only the 9-type notices to get their details (leftJoin to include cases even when 9-type doesn't exist)
            ->leftJoin('notices as notice9', function ($join) {
                $join->on('notice9.file_case_id', '=', 'file_cases.id')
                    ->where('notice9.notice_type', 9);
            })

            ->leftJoin('notices as notice8', function ($join) {
                $join->on('notice8.file_case_id', '=', 'file_cases.id')
                    ->where('notice8.notice_type', 8);
            })

            ->leftJoin('notices as notice11', function ($join) {
                $join->on('notice11.file_case_id', '=', 'file_cases.id')
                    ->where('notice11.notice_type', 11);
            })

        // Do NOT join type 1 notices directly; use whereHas for filter
            ->whereHas('notices', function ($query) {
                $query->where('notice_type', 1)
                    ->whereRaw('DATEDIFF(CURDATE(), DATE(CONVERT_TZ(notices.notice_date, "+00:00", "-06:00"))) >= organization_notice_timelines.notice_4a');
            })

        // Apply condition for type 9 notices (existing and statuses are not fully sent OR doesn't exist)
            ->where(function ($query) {
                $query->whereDoesntHave('notices', function ($q) {
                    $q->where('notice_type', 9);
                })->orWhereHas('notices', function ($q) {
                    $q->where('notice_type', 9)
                        ->where(function ($inner) {
                            $inner->where('email_status', 0);
                        });
                });
            })

        // NEW condition: must also have notice_type = 8, 11
            ->whereHas('notices', function ($q) {
                $q->where('notice_type', 8);
            })

            ->whereHas('notices', function ($q) {
                $q->where('notice_type', 11);
            })

        // Filter by timeline values
            ->whereIn('organization_notice_timelines.notice_4a', function ($query) {
                $query->select('notice_4a')
                    ->from('organization_notice_timelines')
                    ->whereNull('deleted_at')
                    ->whereRaw('organization_notice_timelines.organization_list_id = organization_lists.id');
            })

            ->select(
                'file_cases.*',
                'notice9.notice as notice9',
                'notice8.notice as notice8',
                'notice11.notice as notice11',
                'notice9.email_status as email_status9',
                'organization_notice_timelines.notice_4a',
                DB::raw('org_with_parent.effective_parent_id as parent_id'),
                DB::raw('org_with_parent.effective_parent_name as parent_name')
            )
            ->distinct()
            ->limit(1)
            ->get();

        foreach ($caseData as $key => $value) {
            try {
                $assigncaseData = AssignCase::where('case_id', $value->id)->first();

                if (! empty($assigncaseData)) {
                    $arbitratorIds   = explode(',', $assigncaseData->arbitrator_id);
                    $arbitratorsName = Drp::whereIn('id', $arbitratorIds)->pluck('name')->implode(', ');
                    $arbitratorsData = Drp::whereIn('id', $arbitratorIds)->first();

                    $casemanagerData = Drp::where('id', $assigncaseData->case_manager_id)->first();

                    $noticetemplateData = NoticeTemplate::where('id', 9)->first();
                    $noticeTemplate     = $noticetemplateData->notice;
                    $now                = now();

                    $fileCaseId = $value->id;
                    Log::info("Processing Stage 4A Notice - Email for FileCase ID: {$fileCaseId}");

                    // #########################################################
                    // ################# Send Email using SMTP #################
                    if (empty($value->notice9)) {

                        // Define your replacement values
                        $data = [
                            "ARBITRATOR'S NAME"                             => $arbitratorsName ?? '',
                            "CASE MANAGER'S NAME"                           => $casemanagerData->name ?? '',
                            'PHONE NUMBER'                                  => $casemanagerData->mobile ?? '',
                            'EMAIL ADDRESS'                                 => ($casemanagerData->address1 ?? '') . '&nbsp;' . ($casemanagerData->address2 ?? ''),

                            'CASE REGISTRATION NUMBER'                      => $value->case_number ?? '',
                            'BANK/ORGANISATION/CLAIMANT NAME'               => ($value->claimant_first_name ?? '') . '&nbsp;' . ($value->claimant_last_name ?? ''),
                            'BANK/ORGANISATION/CLAIMANT REGISTERED ADDRESS' => ($value->claimant_address1 ?? '') . '&nbsp;' . ($value->claimant_address2 ?? ''),

                            'FORECLOSURE DATE'                              => $value->file_case_details->foreclosure_amount_date ?? '',
                            'LOAN NO'                                       => $value->loan_number ?? '',
                            'AGREEMENT DATE'                                => $value->agreement_date ?? '',
                            'FINANCE AMOUNT'                                => $value->file_case_details->finance_amount ?? '',
                            'FORECLOSURE AMOUNT'                            => $value->file_case_details->foreclosure_amount ?? '',
                            'CLAIM SIGNATORY/AUTHORISED OFFICER NAME'       => $value->file_case_details->claim_signatory_authorised_officer_name ?? '',
                            'CLAIM SIGNATORY/AUTHORISED OFFICER MOBILE NO'  => $value->file_case_details->claim_signatory_authorised_officer_mobile_no ?? '',
                            "CLAIM SIGNATORY/AUTHORISED OFFICER'S MAIL ID"  => $value->file_case_details->claim_signatory_authorised_officer_mail_id ?? '',

                            "ARBITRATOR'S NAME"                             => $arbitratorsData->name ?? '',
                            "ARBITRATOR'S SPECIALIZATION"                   => $arbitratorsData->specialization ?? '',
                            "ARBITRATOR'S MOBILE NO"                        => $arbitratorsData->mobile ?? '',
                            "ARBITRATOR'S ADDRESS"                          => ($arbitratorsData->address1 ?? '') . '&nbsp;' . ($arbitratorsData->address2 ?? ''),

                            'ARBITRATION CLAUSE NO'                         => $value->arbitration_clause_no ?? '',
                            'ARBITRATION DATE'                              => $value->arbitration_date ?? '',
                            'TENURE'                                        => $value->file_case_details->tenure ?? '',
                            'PRODUCT'                                       => $value->file_case_details->product ?? '',

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

                            'CUSTOMER NAME'                                 => ($value->respondent_first_name ?? '') . '&nbsp;' . ($value->respondent_last_name ?? ''),
                            'CUSTOMER ADDRESS'                              => ($value->respondent_address1 ?? '') . '&nbsp;' . ($value->respondent_address2 ?? ''),
                            'CUSTOMER MOBILE NO'                            => $value->respondent_mobile ?? '',
                            'CUSTOMER MAIL ID'                              => $value->respondent_email ?? '',

                            'DATE'                                          => now()->format('d-m-Y'),

                            'STAGE 1 NOTICE DATE'                           => $value->file_case_details->stage_1_notice_date ?? '',
                            'STAGE 1A NOTICE DATE'                          => $value->file_case_details->stage_1a_notice_date ?? '',
                            'STAGE 1B NOTICE DATE'                          => $value->file_case_details->stage_1b_notice_date ?? '',
                            'STAGE 2B NOTICE DATE'                          => $value->file_case_details->stage_2b_notice_date ?? '',
                            'STAGE 3A NOTICE DATE'                          => $value->file_case_details->stage_3a_notice_date ?? '',
                            'STAGE 3B NOTICE DATE'                          => $value->file_case_details->stage_3b_notice_date ?? '',
                            'STAGE 3C NOTICE DATE'                          => $value->file_case_details->stage_3c_notice_date ?? '',
                            'STAGE 3D NOTICE DATE'                          => $value->file_case_details->stage_3d_notice_date ?? '',
                            'STAGE 4A NOTICE DATE'                          => now()->format('d-m-Y'),
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

                        // $signature = Setting::where('setting_type', '1')->get()->pluck('filed_value', 'setting_name')->toArray();

                        // Image URLs
                        // $headerImg = url('storage/' . $signature['mediateway_letterhead']);
                        // $signatureImg = asset('storage/' . $signature['mediateway_signature']);
                        $signatureImg = url('storage/' . $arbitratorsData->signature_drp);

                        // 🟢 Add header image at the top
                        // $headerHtml = '
                        //     <div style="text-align: center; margin-bottom: 20px;">
                        //         <img src="' . $headerImg . '" style="width: 100%; max-height: 120px;" alt="Header">
                        //     </div>
                        // ';

                        // Add signature at the bottom
                        $finalNotice .= '
                            <div style="text-align: right; margin-top: 0px;">
                                <img src="' . $signatureImg . '" style="height: 80px;" alt="Signature">
                            </div>
                        ';

                        // Prepare HTML
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
                            'notice_' . time() . '.pdf',
                            'application/pdf',
                            null,
                            true
                        );

                        // Save the PDF using your helper
                        $savedPath = Helper::saveFile($uploadedFile, 'notices');

                        $now = now();

                        $notice = Notice::create([
                            'file_case_id'               => $value->id,
                            'notice_type'                => 9,
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
                                    'stage_4a_notice_date' => $now->format('Y-m-d'),
                                ]);
                        }

                        //Send Notice for Assign Arbitrator
                        $data = Setting::where('setting_type', '3')->get()->pluck('filed_value', 'setting_name')->toArray();

                        Config::set("mail.mailers.smtp", [
                            'transport'  => 'smtp',
                            'host'       => $data['smtp_host'],
                            'port'       => $data['smtp_port'],
                            'encryption' => in_array((int) $data['smtp_port'], [587, 2525]) ? 'tls' : 'ssl',
                            'username'   => $data['smtp_user'],
                            'password'   => $data['smtp_pass'],
                            'timeout'    => null,
                            'auth_mode'  => null,
                        ]);

                        Config::set("mail.from", [
                            'address' => $data['email_from'],
                            'name'    => config('app.name'),
                        ]);

                        // ############# Send Email using Email Address #############
                        if (! empty($value->respondent_email)) {
                            $email = strtolower(filter_var(trim($value->respondent_email), FILTER_SANITIZE_EMAIL));

                            $validator = Validator::make(['email' => $email], [
                                'email' => 'required|email:rfc,dns',
                            ]);

                            if ($validator->fails()) {
                                Log::warning("Invalid email address: $email");
                                Notice::where('file_case_id', $value->id)->where('notice_type', 9)
                                    ->update([
                                        'email_status' => 2,
                                    ]);
                            } else {

                                $subject     = $noticetemplateData->subject;
                                $description = 'https://mediateway.com/drp/login';

                                try {
                                    Mail::send('emails.simple', compact('subject', 'description'), function ($message) use ($value, $savedPath, $subject, $email) {
                                        $message->to($email)
                                            ->cc('legaldesk@rblbank.com')
                                            ->subject($subject);
                                        // ->attach(url(str_replace('\\', '/', 'public/storage/' . $savedPath)), [
                                        //     'mime' => 'application/pdf',
                                        // ]);
                                        if (! empty($value->notice9)) {
                                            $message->attach(public_path(str_replace('\\', '/', 'storage/' . $savedPath)), [
                                                'mime' => 'application/pdf',
                                            ]);
                                        }

                                        // Attach notice8 if exists
                                        if (! empty($value->notice8)) {
                                            $message->attach(public_path(str_replace('\\', '/', 'storage/' . $value->notice8)), [
                                                'mime' => 'application/pdf',
                                            ]);
                                        }
                                    });
                                    // Success
                                    Notice::where('file_case_id', $value->id)->where('notice_type', 9)
                                        ->update([
                                            'notice_send_date' => $now,
                                            'email_status'     => 1,
                                        ]);
                                    Log::info("Stage 4A Email sent successfully for FileCase ID: {$fileCaseId}");
                                } catch (\Exception $e) {
                                    Log::error("Notice 4A Failed to send email to: $email. FileCase ID: {$fileCaseId}. Error: " . $e->getMessage());
                                    Notice::where('file_case_id', $value->id)->where('notice_type', 9)
                                        ->update([
                                            'email_status' => 2,
                                        ]);
                                }
                            }
                        }
                    } elseif ($value->email_status9 === 0) {

                        // #########################################################
                        // ################# Send Email using SMTP #################

                        $data = Setting::where('setting_type', '3')->get()->pluck('filed_value', 'setting_name')->toArray();

                        Config::set("mail.mailers.smtp", [
                            'transport'  => 'smtp',
                            'host'       => $data['smtp_host'],
                            'port'       => $data['smtp_port'],
                            'encryption' => in_array((int) $data['smtp_port'], [587, 2525]) ? 'tls' : 'ssl',
                            'username'   => $data['smtp_user'],
                            'password'   => $data['smtp_pass'],
                            'timeout'    => null,
                            'auth_mode'  => null,
                        ]);

                        Config::set("mail.from", [
                            'address' => $data['email_from'],
                            'name'    => config('app.name'),
                        ]);

                        // ###################################################################
                        // ################# Send Email using Email Address ##################
                        if (! empty($value->respondent_email)) {
                            $email = strtolower(filter_var(trim($value->respondent_email), FILTER_SANITIZE_EMAIL));

                            $validator = Validator::make(['email' => $email], [
                                'email' => 'required|email:rfc,dns',
                            ]);

                            if ($validator->fails()) {
                                Log::warning("Invalid email address: $email");
                                Notice::where('file_case_id', $value->id)->where('notice_type', 9)
                                    ->update([
                                        'email_status' => 2,
                                    ]);
                            } else {

                                $subject     = $noticetemplateData->subject;
                                $description = $noticetemplateData->email_content;

                                try {
                                    Mail::send('emails.simple', compact('subject', 'description'), function ($message) use ($value, $subject, $email) {
                                        $message->to($email)
                                            ->subject($subject);
                                        // ->attach(public_path(str_replace('\\', '/', $notice)), [
                                        //     'mime' => 'application/pdf',
                                        // ]);
                                        // Attach notice9 if exists
                                        if (! empty($value->notice9)) {
                                            $message->attach(public_path(str_replace('\\', '/', 'storage/' . $value->notice9)), [
                                                'mime' => 'application/pdf',
                                            ]);
                                        }

                                        // Attach notice8 if exists
                                        if (! empty($value->notice8)) {
                                            $message->attach(public_path(str_replace('\\', '/', 'storage/' . $value->notice8)), [
                                                'mime' => 'application/pdf',
                                            ]);
                                        }
                                    });

                                    Notice::where('file_case_id', $value->id)->where('notice_type', 9)
                                        ->update([
                                            'notice_send_date' => $now,
                                            'email_status'     => 1,
                                        ]);
                                    Log::info("Stage 4A Email sent successfully for FileCase ID: {$fileCaseId}");
                                } catch (\Exception $e) {
                                    Log::error("Notice 4A Failed to send email to: $email. FileCase ID: {$fileCaseId}. Error: " . $e->getMessage());
                                    Notice::where('file_case_id', $value->id)->where('notice_type', 9)
                                        ->update([
                                            'email_status' => 2,
                                        ]);
                                }
                            }
                        }
                    }
                }
            } catch (\Throwable $th) {
                Log::error("Error sending Notice 4A email for record ID {$value->id}: " . $th->getMessage());
            }
        }
    }
}
