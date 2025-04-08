<?php
namespace App\Imports;

use App\Models\City;
use App\Models\FileCase;
use App\Models\FileCaseDetail;
use App\Models\Guarantor;
use App\Models\Organization;
use App\Models\OrganizationList;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class FileCaseImport implements ToModel, WithHeadingRow
{
    protected $organizationId;

    protected $expectedFields = [
        'loan_number', 'agreement_date', 'loan_application_date', 'arbitration_date', 'claimant_first_name',
        'claimant_middle_name', 'claimant_last_name', 'claimant_mobile', 'claimant_email',
        'claimant_address_type', 'claimant_address1', 'claimant_state', 'claimant_city', 'claimant_pincode',
        'respondent_first_name', 'respondent_middle_name', 'respondent_last_name', 'respondent_mobile', 'respondent_email',
        'respondent_address_type', 'respondent_address1', 'respondent_state', 'respondent_city', 'respondent_pincode',
        'amount_in_dispute', 'case_type', 'brief_of_case', 'product', 'asset_description', 'registration_no', 'chassis_no',
        'engin_no', 'finance_amount', 'finance_amount_in_words', 'emi_amt', 'tenure', 'foreclosure_amount_date',
        'foreclosure_amount', 'foreclosure_amount_in_words', 'claim_signatory_authorised_officer_name',
        'claim_signatory_authorised_officer_father_name', 'claim_signatory_authorised_officer_designation',
        'claim_signatory_authorised_officer_mobile_no', 'claim_signatory_authorised_officer_mail_id',
        'receiver_name', 'receiver_designation',
        'guarantor_1_name', 'guarantor_1_mobile_no', 'guarantor_1_email_id', 'guarantor_1_father_name', 'guarantor_1_address',
        'guarantor_2_name', 'guarantor_2_mobile_no', 'guarantor_2_email_id', 'guarantor_2_father_name', 'guarantor_2_address',
        'guarantor_3_name', 'guarantor_3_mobile_no', 'guarantor_3_email_id', 'guarantor_3_father_name', 'guarantor_3_address',
        'guarantor_4_name', 'guarantor_4_mobile_no', 'guarantor_4_email_id', 'guarantor_4_father_name', 'guarantor_4_address',
        'guarantor_5_name', 'guarantor_5_mobile_no', 'guarantor_5_email_id', 'guarantor_5_father_name', 'guarantor_5_address',
        'guarantor_6_name', 'guarantor_6_mobile_no', 'guarantor_6_email_id', 'guarantor_6_father_name', 'guarantor_6_address',
        'guarantor_7_name', 'guarantor_7_mobile_no', 'guarantor_7_email_id', 'guarantor_7_father_name', 'guarantor_7_address',
    ];

    public function __construct($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    public function model(array $row)
    {
        $org = Organization::find($this->organizationId);

        if ($org && $org->parent_id != 0) {
            // Staff or sub-organization, fetch parent organization
            $organizationData = Organization::find($org->parent_id);
        } else {
            // Main/top-level organization
            $organizationData = $org;
        }

        // Normalize Excel header keys
        $normalizedRow = [];
        foreach ($row as $key => $value) {
            $newKey = strtolower(str_replace(' ', '_', trim($key)));
            // Keep only expected keys
            if (in_array($newKey, $this->expectedFields)) {
                $normalizedRow[$newKey] = $value;
            } else {
                Log::info("Skipping unexpected column: $newKey");
            }
        }
        $row = $normalizedRow;

        // Required field: loan_number
        if (empty($row['loan_number'])) {
            Log::warning("Skipped record due to missing loan_number", $row);
            return null;
        }

        // Convert state & city names to IDs
        $claimantState   = $organizationData->state_id;
        $claimantCity    = $organizationData->city_id;
        $respondentState = State::whereRaw('LOWER(name) = ?', [strtolower(trim($row['respondent_state'] ?? ''))])->value('id');
        $respondentCity  = City::whereRaw('LOWER(name) = ?', [strtolower(trim($row['respondent_city'] ?? ''))])->value('id');

        if (! empty($row['loan_number'])) {
            $existingCase = FileCase::where('loan_number', $row['loan_number'])->first();

            if (! $existingCase) {
                // Step 1: Get organization code
                $orgCode = OrganizationList::where('name', $organizationData->name)->value('code');

                // Step 2: Get today's date
                $datePart = now()->format('d-m-Y');

                // Step 3: Count existing cases today for that org
                $prefix = $orgCode . '-' . $datePart;

                $lastCase = FileCase::where('case_number', 'like', "$orgCode-%-$datePart")
                    ->orderBy('case_number', 'desc')
                    ->first();

                // Step 4: Extract last increment and increase
                if ($lastCase && preg_match('/' . $orgCode . '-(\d+)-' . $datePart . '/', $lastCase->case_number, $matches)) {
                    $increment = (int) $matches[1] + 1;
                } else {
                    $increment = 1;
                }

                // Step 5: Build full case number
                $caseNumber = sprintf('%s-%06d-%s', $orgCode, $increment, $datePart);

                // Create FileCase
                $fileCase = new FileCase([
                    'user_type'               => 2,
                    'organization_id'         => $this->organizationId,
                    'case_number'             => $caseNumber ?? null,
                    'loan_number'             => $row['loan_number'] ?? null,
                    'agreement_date'          => ! empty($row['agreement_date']) ? Carbon::parse($row['agreement_date'])->format('Y-m-d') : null,
                    'loan_application_date'   => ! empty($row['loan_application_date']) ? Carbon::parse($row['loan_application_date'])->format('Y-m-d') : null,
                    'arbitration_date'        => ! empty($row['arbitration_date']) ? Carbon::parse($row['arbitration_date'])->format('Y-m-d') : null,
                    'claimant_first_name'     => $organizationData->name ?? null,
                    'claimant_middle_name'    => $row['claimant_middle_name'] ?? null,
                    'claimant_last_name'      => $row['claimant_last_name'] ?? null,
                    'claimant_mobile'         => $organizationData->mobile ?? null,
                    'claimant_email'          => $organizationData->email ?? null,
                    'claimant_address_type'   => 2 ?? null,
                    'claimant_address1'       => $organizationData->address1 ?? null,
                    'claimant_state_id'       => $claimantState,
                    'claimant_city_id'        => $claimantCity,
                    'claimant_pincode'        => $organizationData->pincode ?? null,
                    'respondent_first_name'   => $row['respondent_first_name'] ?? null,
                    'respondent_middle_name'  => $row['respondent_middle_name'] ?? null,
                    'respondent_last_name'    => $row['respondent_last_name'] ?? null,
                    'respondent_mobile'       => $row['respondent_mobile'] ?? null,
                    'respondent_email'        => $row['respondent_email'] ?? null,
                    'respondent_address_type' => $row['respondent_address_type'] ?? null,
                    'respondent_address1'     => $row['respondent_address1'] ?? null,
                    'respondent_state_id'     => $respondentState,
                    'respondent_city_id'      => $respondentCity,
                    'respondent_pincode'      => $row['respondent_pincode'] ?? null,
                    'amount_in_dispute'       => $row['amount_in_dispute'] ?? null,
                    'case_type'               => $row['case_type'] ?? null,
                    'brief_of_case'           => $row['brief_of_case'] ?? null,
                ]);

                $fileCase->save();

                // FileCaseDetail
                FileCaseDetail::create([
                    'file_case_id'                                   => $fileCase->id,
                    'product'                                        => $row['product'] ?? null,
                    'asset_description'                              => $row['asset_description'] ?? null,
                    'registration_no'                                => $row['registration_no'] ?? null,
                    'chassis_no'                                     => $row['chassis_no'] ?? null,
                    'engin_no'                                       => $row['engin_no'] ?? null,
                    'finance_amount'                                 => $row['finance_amount'] ?? null,
                    'finance_amount_in_words'                        => $row['finance_amount_in_words'] ?? null,
                    'emi_amt'                                        => $row['emi_amt'] ?? null,
                    'tenure'                                         => $row['tenure'] ?? null,
                    'foreclosure_amount_date'                        => $row['foreclosure_amount_date'] ?? null,
                    'foreclosure_amount'                             => $row['foreclosure_amount'] ?? null,
                    'foreclosure_amount_in_words'                    => $row['foreclosure_amount_in_words'] ?? null,
                    'claim_signatory_authorised_officer_name'        => $row['claim_signatory_authorised_officer_name'] ?? null,
                    'claim_signatory_authorised_officer_father_name' => $row['claim_signatory_authorised_officer_father_name'] ?? null,
                    'claim_signatory_authorised_officer_designation' => $row['claim_signatory_authorised_officer_designation'] ?? null,
                    'claim_signatory_authorised_officer_mobile_no'   => $row['claim_signatory_authorised_officer_mobile_no'] ?? null,
                    'claim_signatory_authorised_officer_mail_id'     => $row['claim_signatory_authorised_officer_mail_id'] ?? null,
                    'receiver_name'                                  => $row['receiver_name'] ?? null,
                    'receiver_designation'                           => $row['receiver_designation'] ?? null,
                ]);

                // Guarantor
                Guarantor::create([
                    'file_case_id'            => $fileCase->id,
                    'guarantor_1_name'        => $row['guarantor_1_name'] ?? null,
                    'guarantor_1_mobile_no'   => $row['guarantor_1_mobile_no'] ?? null,
                    'guarantor_1_email_id'    => $row['guarantor_1_email_id'] ?? null,
                    'guarantor_1_father_name' => $row['guarantor_1_father_name'] ?? null,
                    'guarantor_1_address'     => $row['guarantor_1_address'] ?? null,

                    'guarantor_2_name'        => $row['guarantor_2_name'] ?? null,
                    'guarantor_2_mobile_no'   => $row['guarantor_2_mobile_no'] ?? null,
                    'guarantor_2_email_id'    => $row['guarantor_2_email_id'] ?? null,
                    'guarantor_2_father_name' => $row['guarantor_2_father_name'] ?? null,
                    'guarantor_2_address'     => $row['guarantor_2_address'] ?? null,

                    'guarantor_3_name'        => $row['guarantor_3_name'] ?? null,
                    'guarantor_3_mobile_no'   => $row['guarantor_3_mobile_no'] ?? null,
                    'guarantor_3_email_id'    => $row['guarantor_3_email_id'] ?? null,
                    'guarantor_3_father_name' => $row['guarantor_3_father_name'] ?? null,
                    'guarantor_3_address'     => $row['guarantor_3_address'] ?? null,

                    'guarantor_4_name'        => $row['guarantor_4_name'] ?? null,
                    'guarantor_4_mobile_no'   => $row['guarantor_4_mobile_no'] ?? null,
                    'guarantor_4_email_id'    => $row['guarantor_4_email_id'] ?? null,
                    'guarantor_4_father_name' => $row['guarantor_4_father_name'] ?? null,
                    'guarantor_4_address'     => $row['guarantor_4_address'] ?? null,

                    'guarantor_5_name'        => $row['guarantor_5_name'] ?? null,
                    'guarantor_5_mobile_no'   => $row['guarantor_5_mobile_no'] ?? null,
                    'guarantor_5_email_id'    => $row['guarantor_5_email_id'] ?? null,
                    'guarantor_5_father_name' => $row['guarantor_5_father_name'] ?? null,
                    'guarantor_5_address'     => $row['guarantor_5_address'] ?? null,

                    'guarantor_6_name'        => $row['guarantor_6_name'] ?? null,
                    'guarantor_6_mobile_no'   => $row['guarantor_6_mobile_no'] ?? null,
                    'guarantor_6_email_id'    => $row['guarantor_6_email_id'] ?? null,
                    'guarantor_6_father_name' => $row['guarantor_6_father_name'] ?? null,
                    'guarantor_6_address'     => $row['guarantor_6_address'] ?? null,

                    'guarantor_7_name'        => $row['guarantor_7_name'] ?? null,
                    'guarantor_7_mobile_no'   => $row['guarantor_7_mobile_no'] ?? null,
                    'guarantor_7_email_id'    => $row['guarantor_7_email_id'] ?? null,
                    'guarantor_7_father_name' => $row['guarantor_7_father_name'] ?? null,
                    'guarantor_7_address'     => $row['guarantor_7_address'] ?? null,
                ]);

            } else {
                Log::info('Loan No Already Exist.');
            }
        }
        return null;
    }

}
