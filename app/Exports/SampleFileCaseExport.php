<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SampleFileCaseExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'case_type',
            'claimant_first_name',
            'claimant_middle_name',
            'claimant_last_name',
            'claimant_mobile',
            'claimant_email',
            'claimant_address_type',
            'claimant_address1',
            'claimant_state_id',
            'claimant_city_id',
            'claimant_pincode',
            'respondent_first_name',
            'respondent_middle_name',
            'respondent_last_name',
            'respondent_mobile',
            'respondent_email',
            'respondent_address_type',
            'respondent_address1',
            'respondent_state_id',
            'respondent_city_id',
            'respondent_pincode',
            'amount_in_dispute',
            'brief_of_case',
        ];
    }

    public function array(): array
    {
        return [
            [
                '2', 'John', null, 'Doe', '9876543210', 'john@example.com', '1', 
                '123 Main St', 'Rajasthan', 'Jodhpur', '90001', 
                'Jane', null, 'Smith', '9876543211', 'jane@example.com', '2', 
                '456 Park Ave', 'Rajasthan', 'Ajmer', '77001', 
                '5000', 'Case regarding property dispute', null
            ]
        ];
    }
}

