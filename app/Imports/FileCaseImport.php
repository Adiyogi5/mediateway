<?php
namespace App\Imports;

use App\Models\City;
use App\Models\FileCase;
use App\Models\State;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class FileCaseImport implements ToModel, WithHeadingRow
{
    protected $organizationId;

    public function __construct($organizationId)
    {
        $this->organizationId = $organizationId;
    }

    public function model(array $row)
    {
        // Required fields that must not be empty
        $requiredFields = ['case_type', 'claimant_first_name', 'claimant_mobile', 'claimant_email', 'claimant_address_type', 'claimant_address1', 'claimant_state_id', 'claimant_city_id', 'claimant_pincode', 'respondent_first_name', 'respondent_mobile', 'respondent_email', 'respondent_address_type', 'respondent_address1', 'respondent_state_id', 'respondent_city_id', 'respondent_pincode', 'amount_in_dispute', 'brief_of_case'];
    
        // Check if required fields have values
        foreach ($requiredFields as $field) {
            if (!isset($row[$field]) || trim($row[$field]) === '') {
                Log::warning("Skipped record due to missing field: $field", $row);
                return null; // Skip this row if any required field is missing
            }
        }
    
        // Convert state_id to name
        $claimantState = State::where('name', trim($row['claimant_state_id'] ?? ''))->value('id');
        $respondentState = State::where('name', trim($row['respondent_state_id'] ?? ''))->value('id');

        $claimantCity    = City::where('name', trim($row['claimant_city_id'] ?? ''))->value('id');
        $respondentCity  = City::where('name', trim($row['respondent_city_id'] ?? ''))->value('id');

    
        return new FileCase([
            'user_type'               => 2,
            'organization_id'         => $this->organizationId,
            'claimant_first_name'     => $row['claimant_first_name'] ?? null,
            'claimant_middle_name'    => $row['claimant_middle_name'] ?? null,
            'claimant_last_name'      => $row['claimant_last_name'] ?? null,
            'claimant_mobile'         => $row['claimant_mobile'] ?? null,
            'claimant_email'          => $row['claimant_email'] ?? null,
            'claimant_address_type'   => $row['claimant_address_type'] ?? null,
            'claimant_address1'       => $row['claimant_address1'] ?? null,
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
            'respondent_state_id'     => $respondentState,
            'respondent_city_id'      => $respondentCity,
            'respondent_pincode'      => $row['respondent_pincode'] ?? null,
            'amount_in_dispute'       => $row['amount_in_dispute'] ?? null,
            'case_type'               => $row['case_type'] ?? null,
            'brief_of_case'           => $row['brief_of_case'] ?? null,
        ]);
    }
    
}
