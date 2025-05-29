<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SampleFileCaseExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        $fields = [
            // Required Field
            'loan_number',

            // FileCase fields
            'case_type',
            'product_type',
            'agreement_date',
            'loan_application_date',
            'arbitration_clause_no',
            'arbitration_date',
            // 'claimant_first_name',
            // 'claimant_middle_name',
            // 'claimant_last_name',
            // 'claimant_mobile',
            // 'claimant_email',
            // 'claimant_address_type',
            // 'claimant_address1',
            // 'claimant_state',
            // 'claimant_city',
            // 'claimant_pincode',
            'respondent_first_name',
            'respondent_middle_name',
            'respondent_last_name',
            'respondent_mobile',
            'respondent_email',
            'respondent_address_type',
            'respondent_address1',
            'respondent_state',
            'respondent_city',
            'respondent_pincode',
            'amount_in_dispute',
            'brief_of_case',

            // FileCaseDetail 
            'product',
            'asset_description',
            'sanction_letter_date',
            'rate_of_interest',
            'registration_no',
            'chassis_no',
            'engin_no',
            'finance_amount',
            'finance_amount_in_words',
            'emi_amt',
            'emi_due_date',
            'tenure',
            'foreclosure_amount_date',
            'foreclosure_amount',
            'foreclosure_amount_in_words',
            'claim_signatory_authorised_officer_name',
            'claim_signatory_authorised_officer_father_name',
            'claim_signatory_authorised_officer_designation',
            'claim_signatory_authorised_officer_mobile_no',
            'claim_signatory_authorised_officer_mail_id',
            'receiver_name',
            'receiver_designation',
            'auction_date',
            'auction_amount',
            'auction_amount_in_words',

            // Guarantor fields 
            'guarantor_1_name',
            'guarantor_1_mobile_no',
            'guarantor_1_email_id',
            'guarantor_1_father_name',
            'guarantor_1_address',
            'guarantor_2_name',
            'guarantor_2_mobile_no',
            'guarantor_2_email_id',
            'guarantor_2_father_name',
            'guarantor_2_address',
            'guarantor_3_name',
            'guarantor_3_mobile_no',
            'guarantor_3_email_id',
            'guarantor_3_father_name',
            'guarantor_3_address',
            'guarantor_4_name',
            'guarantor_4_mobile_no',
            'guarantor_4_email_id',
            'guarantor_4_father_name',
            'guarantor_4_address',
            'guarantor_5_name',
            'guarantor_5_mobile_no',
            'guarantor_5_email_id',
            'guarantor_5_father_name',
            'guarantor_5_address',
            'guarantor_6_name',
            'guarantor_6_mobile_no',
            'guarantor_6_email_id',
            'guarantor_6_father_name',
            'guarantor_6_address',
            'guarantor_7_name',
            'guarantor_7_mobile_no',
            'guarantor_7_email_id',
            'guarantor_7_father_name',
            'guarantor_7_address',
        ];

        return array_map(function ($field) {
            return strtoupper(str_replace('_', ' ', $field));
        }, $fields);
    }

    public function array(): array
    {
        return [
            [
                'LN123456', // loan_number (required)

                // FileCase sample values
                '2', '1', '2025-03-15', '2025-03-15', 'ABC1234', '2025-03-15',
                // 'John', null, 'Doe', '9876543210', 'john@example.com', '1',
                // '123 Main St', 'Rajasthan', 'Jodhpur', '90001',
                'Jane', null, 'Smith', '9876543211', 'jane@example.com', '2',
                '456 Park Ave', 'Rajasthan', 'Ajmer', '77001',
                '5000', 'Case regarding loan default',

                // FileCaseDetail sample values
                'Car Loan', 'Hyundai i20', '2025-03-15', '10', 'RJ19AB1234', 'CH123456', 'EN654321',
                '500000', 'Five Lakh Rupees Only', '12000', '2025-03-15', '48',
                '2025-03-15', '300000', 'Three Lakh Rupees Only',
                'Anil Mehta', 'Raj Mehta', 'Manager', '9988776655', 'anil@example.com',
                'Rakesh Sharma', 'Legal Head', '2025-03-15', '300000', 'Three Lakh Rupees Only',

                // Guarantors 1 to 7 (only sample for first 2)
                'Rahul Sharma', '9998887771', 'rahul@example.com', 'Vijay Sharma', 'Bangalore, India',
                'Suresh Kumar', '8887776661', 'suresh@example.com', 'Rajesh Kumar', 'Chennai, India',

                // Rest empty for sample
                '', '', '', '', '',
                '', '', '', '', '',
                '', '', '', '', '',
                '', '', '', '', ''
            ]
        ];
    }
}
