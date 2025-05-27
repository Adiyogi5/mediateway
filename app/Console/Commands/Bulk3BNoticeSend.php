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
use Illuminate\Support\Facades\Mail;
use Twilio\Rest\Client;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class Bulk3BNoticeSend extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bulk:send-3b-notice';

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
        // ##################################################
        // Final Appointment Of Arbitrator - 3B - Notice Send
        // ##################################################
        $caseData = FileCase::with('file_case_details')
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
                    ->whereRaw('DATEDIFF(CURDATE(), notices.notice_date) >= organization_notice_timelines.notice_3b');
            })
            ->where(function ($query) {
                $query->whereDoesntHave('notices', function ($q) {
                    $q->where('notice_type', 6);
                })
                    ->orWhereHas('notices', function ($q) {
                        $q->where('notice_type', 6)
                            ->where('email_status', 0);
                    });
            })
            ->whereIn('organization_notice_timelines.notice_3b', function ($query) {
                $query->select('notice_3b')
                    ->from('organization_notice_timelines')
                    ->whereNull('deleted_at')
                    ->whereRaw('organization_notice_timelines.organization_list_id = organization_lists.id');
            })
            ->select(
                'file_cases.*',
                'organization_notice_timelines.notice_3b',
                DB::raw('org_with_parent.effective_parent_id as parent_id'),
                DB::raw('org_with_parent.effective_parent_name as parent_name')
            )
            ->distinct()
            ->get();

        foreach ($caseData as $key => $value) {
            try {
                $assigncaseData = AssignCase::where('case_id', $value->id)->first();
                // $noticedataFetchArbitrator = Notice::where('file_case_id', $value->id)->where('notice_type', 6)->first();
                // dd($noticedataFetchArbitrator);
                if (($assigncaseData->receiveto_casemanager == 1)) {
                    $arbitratorIds   = explode(',', $assigncaseData->arbitrator_id);
                    $arbitratorsName = Drp::whereIn('id', $arbitratorIds)->pluck('name')->implode(', ');
                    $arbitratorsData = Drp::whereIn('id', $arbitratorIds)->first();

                    $casemanagerData = Drp::where('id', $assigncaseData->case_manager_id)->first();

                    $noticetemplateData = NoticeTemplate::where('id', 6)->first();
                    $noticeTemplate     = $noticetemplateData->notice_format;

                    // Define your replacement values
                    $data = [
                        "ARBITRATOR'S NAME"                                               => $arbitratorsName ?? '',
                        "CASE MANAGER'S NAME"                                             => $casemanagerData->name ?? '',
                        'PHONE NUMBER'                                                    => $casemanagerData->mobile ?? '',
                        'EMAIL ADDRESS'                                                   => ($casemanagerData->address1 ?? '') . '&nbsp;' . ($casemanagerData->address2 ?? ''),

                        'CASE REGISTRATION NUMBER'                                        => $value->case_number ?? '',
                        'BANK/ORGANISATION/CLAIMANT NAME'                                 => ($value->claimant_first_name ?? '') . '&nbsp;' . ($value->claimant_last_name ?? ''),
                        'BANK/ORGANISATION/CLAIMANT REGISTERED ADDRESS'                   => ($value->claimant_address1 ?? '') . '&nbsp;' . ($value->claimant_address2 ?? ''),

                        'CLAIM SIGNATORY/AUTHORISED OFFICER MOBILE NO'                    => $value->file_case_details->claim_signatory_authorised_officer_mobile_no ?? '',
                        "CLAIM SIGNATORY/AUTHORISED OFFICER'S MAIL ID"                    => $casvalueeData->file_case_details->claim_signatory_authorised_officer_mail_id ?? '',

                        'LOAN NO'                                                         => $value->loan_number ?? '',
                        'AGREEMENT DATE'                                                  => $value->agreement_date ?? '',
                        'FINANCE AMOUNT'                                                  => $value->file_case_details->finance_amount ?? '',
                        'TENURE'                                                          => $value->file_case_details->tenure ?? '',
                        'FORECLOSURE AMOUNT'                                              => $value->file_case_details->foreclosure_amount ?? '',

                        "ARBITRATOR'S NAME"                                               => $arbitratorsData->name ?? '',
                        "ARBITRATOR'S SPECIALIZATION"                                     => $arbitratorsData->specialization ?? '',
                        "ARBITRATOR'S ADDRESS"                                            => ($arbitratorsData->address1 ?? '') . '&nbsp;' . ($arbitratorsData->address2 ?? ''),
                        'ARBITRATION CLAUSE NO'                                           => $value->arbitration_clause_no ?? '',

                        'CUSTOMER NAME'                                                   => ($value->respondent_first_name ?? '') . '&nbsp;' . ($value->respondent_last_name ?? ''),
                        'CUSTOMER ADDRESS'                                                => ($value->respondent_address1 ?? '') . '&nbsp;' . ($value->respondent_address2 ?? ''),
                        'CUSTOMER MOBILE NO'                                              => $value->respondent_mobile ?? '',
                        'CUSTOMER MAIL ID'                                                => $value->respondent_email ?? '',

                        'DATE'                                                            => now()->format('d-m-Y'),
                        'STAGE 1 NOTICE DATE'                                             => $value->file_case_details->stage_1_notice_date ?? '',
                        'STAGE 2B NOTICE DATE'                                            => $value->file_case_details->stage_2b_notice_date ?? '',
                        'STAGE 3A NOTICE DATE'                                            => $value->file_case_details->stage_3a_notice_date ?? '',
                        'STAGE 3B NOTICE DATE'                                            => now()->format('d-m-Y'),
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

                    $signature = Setting::where('setting_type', '1')->get()->pluck('filed_value', 'setting_name')->toArray();
                    // Append the signature image at the end of the content, aligned right
                    $finalNotice .= '
                        <div style="text-align: right; margin-top: 0px;">
                            <img src="' . asset('storage/' . $signature['mediateway_signature']) . '" style="height: 80px;" alt="Signature">
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
                        'notice_type'                => 6,
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
                                'stage_3b_notice_date' => $now->format('d-m-Y'),
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

                    if (! empty($value->respondent_email)) {
                        $email = filter_var($value->respondent_email, FILTER_SANITIZE_EMAIL);

                        $validator = Validator::make(['email' => $email], [
                            'email' => 'required|email:rfc,dns',
                        ]);

                        if ($validator->fails()) {

                            Log::warning("Invalid email address: $email");
                            $notice->update(['email_status' => 2]);

                        } else {

                            $subject     = $noticetemplateData->subject;
                            $description = $noticetemplateData->email_content;

                            // Mail::send('emails.simple', compact('subject', 'description'), function ($message) use ($savedPath, $subject, $email) {
                            //     $message->to($email)
                            //             ->subject($subject)
                            //             ->attach(public_path(str_replace('\\', '/', $savedPath)), [
                            //                 'mime' => 'application/pdf',
                            //             ]);
                            // });

                            // if (Mail::failures()) {
                            Log::error("Failed to send email to: $email");
                            $notice->update(['email_status' => 2]);
                            // } else {
                            $notice->update(['notice_send_date' => now()]);
                            $notice->update(['email_status' => 1]);
                            // }
                        }
                    }

                    // Send SMS Invitation using Twilio
                    // try {
                    //     $sid    = env("TWILIO_ACCOUNT_SID");
                    //     $token  = env("TWILIO_AUTH_TOKEN");
                    //     $sender = env("TWILIO_SENDER");

                    //     $client = new Client($sid, $token);

                    //     $country_data = Country::where('id', $request->country_id)->where('status', 1)->first();
                    //     $phone_code = $country_data->phone_code ?? '';

                    //     $message = "{$user->name} has invited you to join Patrimonial, an online testament and wealth management App, to securely manage and access patrimonial information. Accept the invitation here: https://www.name/login";

                    //     $client->messages->create($phone_code . $request->mobile, [
                    //         'from' => $sender,
                    //         'body' => $message,
                    //     ]);
                    // } catch (\Throwable $th) {
                    //     // Log SMS error but don't stop execution
                    //     Log::error('SMS sending failed: ' . $th->getMessage());
                    // }
                }
            } catch (\Throwable $th) {
                // Log the error and update the email status
                Log::error("Error sending email for record ID {$value->id}: " . $th->getMessage());
                // $value->update(['email_status' => 2]);
            }
        }
    }
}
