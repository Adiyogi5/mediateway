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
use PhpOffice\PhpSpreadsheet\Shared\Date;

class BulkUpdateCaseImport implements ToModel, WithHeadingRow
{
    public static $unmatchedLoans = [];

    protected $organizationId;

    protected $expectedFields = [
        'case_number', 'loan_number', 'agreement_date', 'loan_application_date', 'arbitration_clause_no', 'arbitration_date', 'claimant_first_name',
        'claimant_middle_name', 'claimant_last_name', 'claimant_mobile', 'claimant_email',
        'claimant_address_type', 'claimant_address1', 'claimant_state', 'claimant_city', 'claimant_pincode',
        'respondent_first_name', 'respondent_middle_name', 'respondent_last_name', 'respondent_mobile', 'respondent_email',
        'respondent_address_type', 'respondent_address1', 'respondent_state', 'respondent_city', 'respondent_pincode',
        'amount_in_dispute', 'case_type', 'brief_of_case', 'product_type', 'product', 'asset_description', 'sanction_letter_date', 'rate_of_interest', 'registration_no', 'chassis_no',
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
        if (empty($row['loan_number'])) {
            Log::warning("Skipped row due to missing loan_number", $row);
            return null;
        }

        // Resolve state and city IDs
        $claimantState   = State::whereRaw('LOWER(name) = ?', [strtolower(trim($row['claimant_state'] ?? ''))])->value('id');
        $claimantCity    = City::whereRaw('LOWER(name) = ?', [strtolower(trim($row['claimant_city'] ?? ''))])->value('id');
        $respondentState = State::whereRaw('LOWER(name) = ?', [strtolower(trim($row['respondent_state'] ?? ''))])->value('id');
        $respondentCity  = City::whereRaw('LOWER(name) = ?', [strtolower(trim($row['respondent_city'] ?? ''))])->value('id');

        // Fetch existing case
        $fileCase = FileCase::where('loan_number', $row['loan_number'])->first();
        if (! $fileCase) {
            self::$unmatchedLoans[] = $row['loan_number'];
            Log::warning("Skipped row: No matching FileCase found for loan_number: {$row['loan_number']}");
            return null;
        }

        $updateData = [];
        $fields     = [
            'loan_number', 'arbitration_clause_no', 'claimant_first_name', 'claimant_middle_name', 'claimant_last_name',
            'claimant_mobile', 'claimant_email', 'claimant_address_type', 'claimant_address1', 'claimant_address2', 'claimant_pincode',
            'respondent_first_name', 'respondent_middle_name', 'respondent_last_name', 'respondent_mobile', 'respondent_email',
            'respondent_address_type', 'respondent_address1', 'respondent_address2', 'respondent_pincode',
            'amount_in_dispute', 'case_type', 'product_type', 'brief_of_case',
        ];

        foreach ($fields as $field) {
            if (! empty($row[$field])) {
                $updateData[$field] = $row[$field];
            }
        }

        // Date fields
        foreach (['agreement_date', 'loan_application_date', 'arbitration_date'] as $dateField) {
            if (! empty($row[$dateField])) {
                $updateData[$dateField] = $this->parseExcelDate($row[$dateField]);
            }
        }

        if (! empty($claimantState)) {
            $updateData['claimant_state_id'] = $claimantState;
        }

        if (! empty($claimantCity)) {
            $updateData['claimant_city_id'] = $claimantCity;
        }

        if (! empty($respondentState)) {
            $updateData['respondent_state_id'] = $respondentState;
        }

        if (! empty($respondentCity)) {
            $updateData['respondent_city_id'] = $respondentCity;
        }

        $fileCase->update($updateData);

        // === FileCaseDetail update logic ===
        $detailData   = [];
        $detailFields = [
            'product', 'asset_description', 'rate_of_interest', 'registration_no', 'chassis_no', 'engin_no',
            'finance_amount', 'finance_amount_in_words', 'emi_amt', 'emi_due_date', 'tenure',
            'foreclosure_amount', 'foreclosure_amount_in_words',
            'claim_signatory_authorised_officer_name', 'claim_signatory_authorised_officer_father_name',
            'claim_signatory_authorised_officer_designation', 'claim_signatory_authorised_officer_mobile_no',
            'claim_signatory_authorised_officer_mail_id', 'receiver_name', 'receiver_designation',
            'auction_amount', 'auction_amount_in_words',
        ];

        foreach ($detailFields as $field) {
            if (! empty($row[$field])) {
                $detailData[$field] = $row[$field];
            }
        }

        foreach (['sanction_letter_date', 'foreclosure_amount_date', 'auction_date'] as $dateField) {
            if (! empty($row[$dateField])) {
                $detailData[$dateField] = $this->parseExcelDate($row[$dateField]);
            }
        }

        FileCaseDetail::updateOrCreate(
            ['file_case_id' => $fileCase->id],
            $detailData
        );

        // === Guarantors update logic ===
        $guarantorData = [];
        foreach (range(1, 7) as $i) {
            foreach (['name', 'mobile_no', 'email_id', 'father_name', 'address'] as $field) {
                $key = "guarantor_{$i}_{$field}";
                if (! empty($row[$key])) {
                    $guarantorData[$key] = $row[$key];
                }
            }
        }

        Guarantor::updateOrCreate(
            ['file_case_id' => $fileCase->id],
            $guarantorData
        );

        return null;
    }

    private function parseExcelDate($date)
    {
        if (empty($date)) {
            return null;
        }

        try {
            if (is_numeric($date)) {
                return Date::excelToDateTimeObject($date)->format('Y-m-d');
            } else {
                return Carbon::parse(trim($date))->format('Y-m-d');
            }
        } catch (\Exception $e) {
            Log::error('Date parsing failed: ' . $date . ' | ' . $e->getMessage());
            return null;
        }
    }

}

