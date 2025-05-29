<?php
namespace App\Imports;

use App\Models\City;
use App\Models\FileCase;
use App\Models\FileCaseDetail;
use App\Models\Guarantor;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BulkUpdateCaseImport implements ToModel, WithHeadingRow
{
    protected $organizationId;

    protected $expectedFields = [
        'case_number', 'loan_number', 'agreement_date', 'loan_application_date', 'arbitration_clause_no', 'arbitration_date', 'claimant_first_name',
        'claimant_middle_name', 'claimant_last_name', 'claimant_mobile', 'claimant_email',
        'claimant_address_type', 'claimant_address1', 'claimant_state', 'claimant_city', 'claimant_pincode',
        'respondent_first_name', 'respondent_middle_name', 'respondent_last_name', 'respondent_mobile', 'respondent_email',
        'respondent_address_type', 'respondent_address1', 'respondent_state', 'respondent_city', 'respondent_pincode',
        'amount_in_dispute', 'case_type', 'brief_of_case', 'product_type', 'product', 'asset_description', 'sanction_letter_date','rate_of_interest', 'registration_no', 'chassis_no',
        'engin_no', 'finance_amount', 'finance_amount_in_words', 'emi_amt', 'emi_due_date', 'tenure', 'foreclosure_amount_date',
        'foreclosure_amount', 'foreclosure_amount_in_words', 'claim_signatory_authorised_officer_name',
        'claim_signatory_authorised_officer_father_name', 'claim_signatory_authorised_officer_designation',
        'claim_signatory_authorised_officer_mobile_no', 'claim_signatory_authorised_officer_mail_id',
        'receiver_name', 'receiver_designation', 'auction_date', 'auction_amount', 'auction_amount_in_words',
        'guarantor_1_name', 'guarantor_1_mobile_no', 'guarantor_1_email_id', 'guarantor_1_father_name', 'guarantor_1_address',
        'guarantor_2_name', 'guarantor_2_mobile_no', 'guarantor_2_email_id', 'guarantor_2_father_name', 'guarantor_2_address',
        'guarantor_3_name', 'guarantor_3_mobile_no', 'guarantor_3_email_id', 'guarantor_3_father_name', 'guarantor_3_address',
        'guarantor_4_name', 'guarantor_4_mobile_no', 'guarantor_4_email_id', 'guarantor_4_father_name', 'guarantor_4_address',
        'guarantor_5_name', 'guarantor_5_mobile_no', 'guarantor_5_email_id', 'guarantor_5_father_name', 'guarantor_5_address',
        'guarantor_6_name', 'guarantor_6_mobile_no', 'guarantor_6_email_id', 'guarantor_6_father_name', 'guarantor_6_address',
        'guarantor_7_name', 'guarantor_7_mobile_no', 'guarantor_7_email_id', 'guarantor_7_father_name', 'guarantor_7_address',
    ];


    public function model(array $row)
    {
        // Normalize header keys to match expected fields
        $normalizedRow = [];
        foreach ($row as $key => $value) {
            $newKey = strtolower(str_replace(' ', '_', trim($key)));
            if (in_array($newKey, $this->expectedFields)) {
                $normalizedRow[$newKey] = $value;
            } else {
                Log::info("Skipping unexpected column: $newKey");
            }
        }
        $row = $normalizedRow;
    
        // Required fields
        if (!isset($row['case_number']) || !isset($row['loan_number'])) {
            Log::warning("Skipped row due to missing case_number or loan_number", $row);
            return null;
        }
    
        // Resolve state and city IDs
        $claimantState   = State::whereRaw('LOWER(name) = ?', [strtolower(trim($row['claimant_state'] ?? ''))])->value('id');
        $claimantCity    = City::whereRaw('LOWER(name) = ?', [strtolower(trim($row['claimant_city'] ?? ''))])->value('id');
        $respondentState = State::whereRaw('LOWER(name) = ?', [strtolower(trim($row['respondent_state'] ?? ''))])->value('id');
        $respondentCity  = City::whereRaw('LOWER(name) = ?', [strtolower(trim($row['respondent_city'] ?? ''))])->value('id');
    
        // Fetch existing case
        $fileCase = FileCase::where('case_number', $row['case_number'])->first();
        if (! $fileCase) {
            Log::warning("Skipped row: No matching FileCase found for case_number: {$row['case_number']}");
            return null;
        }
    
        // Update FileCase
        $fileCase->update([
            'loan_number'             => $row['loan_number'],
            'agreement_date'          => $this->parseDate($row['agreement_date']),
            'loan_application_date'   => $this->parseDate($row['loan_application_date']),
            'arbitration_date'        => $this->parseDate($row['arbitration_date']),
            'arbitration_clause_no'   => $row['arbitration_clause_no'] ?? null,
            'claimant_first_name'     => $row['claimant_first_name'] ?? null,
            'claimant_middle_name'    => $row['claimant_middle_name'] ?? null,
            'claimant_last_name'      => $row['claimant_last_name'] ?? null,
            'claimant_mobile'         => $row['claimant_mobile'] ?? null,
            'claimant_email'          => $row['claimant_email'] ?? null,
            'claimant_address_type'   => $row['claimant_address_type'] ?? null,
            'claimant_address1'       => $row['claimant_address1'] ?? null,
            'claimant_address2'       => $row['claimant_address2'] ?? null,
            'claimant_state_id'       => $claimantState,
            'claimant_city_id'        => $claimantCity,
            'claimant_pincode'        => $row['claimant_pincode'] ?? null,
            'respondent_first_name'   => $row['respondent_first_name'] ?? null,
            'respondent_middle_name'  => $row['respondent_middle_name'] ?? null,
            'respondent_last_name'    => $row['respondent_last_name'] ?? null,
            'respondent_mobile'       => $row['respondent_mobile'] ?? null,
            'respondent_email'        => $row['respondent_email'] ?? null,
            'respondent_address_type' => $row['respondent_address_type'] ?? null,
            'respondent_address1'     => $row['respondent_address1'] ?? null,
            'respondent_address2'     => $row['respondent_address2'] ?? null,
            'respondent_state_id'     => $respondentState,
            'respondent_city_id'      => $respondentCity,
            'respondent_pincode'      => $row['respondent_pincode'] ?? null,
            'amount_in_dispute'       => $row['amount_in_dispute'] ?? null,
            'case_type'               => $row['case_type'] ?? null,
            'product_type'            => $row['product_type'] ?? null,
            'brief_of_case'           => $row['brief_of_case'] ?? null,
        ]);
    
        // FileCaseDetail
        FileCaseDetail::updateOrCreate(
            ['file_case_id' => $fileCase->id],
            [
                'product'                                        => $row['product'] ?? null,
                'asset_description'                              => $row['asset_description'] ?? null,
                'sanction_letter_date'                           => $row['sanction_letter_date'] ?? null,
                'rate_of_interest'                                => $row['rate_of_interest'] ?? null,
                'registration_no'                                => $row['registration_no'] ?? null,
                'chassis_no'                                     => $row['chassis_no'] ?? null,
                'engin_no'                                       => $row['engin_no'] ?? null,
                'finance_amount'                                 => $row['finance_amount'] ?? null,
                'finance_amount_in_words'                        => $row['finance_amount_in_words'] ?? null,
                'emi_amt'                                        => $row['emi_amt'] ?? null,
                'emi_due_date'                                   => $row['emi_due_date'] ?? null,
                'tenure'                                         => $row['tenure'] ?? null,
                'foreclosure_amount_date'                        => $this->parseDate($row['foreclosure_amount_date']),
                'foreclosure_amount'                             => $row['foreclosure_amount'] ?? null,
                'foreclosure_amount_in_words'                    => $row['foreclosure_amount_in_words'] ?? null,
                'claim_signatory_authorised_officer_name'        => $row['claim_signatory_authorised_officer_name'] ?? null,
                'claim_signatory_authorised_officer_father_name' => $row['claim_signatory_authorised_officer_father_name'] ?? null,
                'claim_signatory_authorised_officer_designation' => $row['claim_signatory_authorised_officer_designation'] ?? null,
                'claim_signatory_authorised_officer_mobile_no'   => $row['claim_signatory_authorised_officer_mobile_no'] ?? null,
                'claim_signatory_authorised_officer_mail_id'     => $row['claim_signatory_authorised_officer_mail_id'] ?? null,
                'receiver_name'                                  => $row['receiver_name'] ?? null,
                'receiver_designation'                           => $row['receiver_designation'] ?? null,
                'auction_date'                                   => $row['auction_date'] ?? null,
                'auction_amount'                                 => $row['auction_amount'] ?? null,
                'auction_amount_in_words'                        => $row['auction_amount_in_words'] ?? null,
            ]
        );
    
        // Guarantors
        Guarantor::updateOrCreate(
            ['file_case_id' => $fileCase->id],
            collect(range(1, 7))->mapWithKeys(function ($i) use ($row) {
                return [
                    "guarantor_{$i}_name"        => $row["guarantor_{$i}_name"] ?? null,
                    "guarantor_{$i}_mobile_no"   => $row["guarantor_{$i}_mobile_no"] ?? null,
                    "guarantor_{$i}_email_id"    => $row["guarantor_{$i}_email_id"] ?? null,
                    "guarantor_{$i}_father_name" => $row["guarantor_{$i}_father_name"] ?? null,
                    "guarantor_{$i}_address"     => $row["guarantor_{$i}_address"] ?? null,
                ];
            })->toArray()
        );
    
        return null;
    }

    private function parseDate($date)
    {
        if (empty($date)) return null;
    
        try {
            if (is_numeric($date)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
            }
            return \Carbon\Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            Log::error("Date parsing failed: {$date}", ['error' => $e->getMessage()]);
            return null;
        }
    }    


}
