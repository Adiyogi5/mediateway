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
            if (empty($row[$field])) {
                Log::warning("Skipped record due to missing field: $field", $row);
                return null; // Skip this row if any required field is missing
            }
        }
    
        // Convert state_id to name
        $claimantStateName   = State::where('name', $row['claimant_state'] ?? null)->value('id');
        $respondentStateName = State::where('name', $row['respondent_state'] ?? null)->value('id');
        $claimantCityName    = City::where('name', $row['claimant_city'] ?? null)->value('id');
        $respondentCityName  = City::where('name', $row['respondent_city'] ?? null)->value('id');
    
        return new FileCase([
            'user_type'               => 2,
            'organization_id'         => $this->organizationId,
            'claimant_first_name'     => $row['claimant_first_name'] ?? null,
            'claimant_middle_name'    => $row['claimant_middle_name'] ?? null,
            'claimant_last_name'      => $row['claimant_last_name'] ?? null,
            'claimant_mobile'         => $row['claimant_mobile'] ?? null,
            'claimant_email'          => $row['claimant_email'] ?? null,
            'claimant_address_type'   => $row['claimant_address_type'] ?? null,
            'claimant_address1'       => $row['claimant_address'] ?? null,
            'claimant_state_id'       => $claimantStateName ?? null,
            'claimant_city_id'        => $claimantCityName ?? null,
            'claimant_pincode'        => $row['claimant_pincode'] ?? null,
            'respondent_first_name'   => $row['respondent_first_name'] ?? null,
            'respondent_middle_name'  => $row['respondent_middle_name'] ?? null,
            'respondent_last_name'    => $row['respondent_last_name'] ?? null,
            'respondent_mobile'       => $row['respondent_mobile'] ?? null,
            'respondent_email'        => $row['respondent_email'] ?? null,
            'respondent_address_type' => $row['respondent_address_type'] ?? null,
            'respondent_address1'     => $row['respondent_address'] ?? null,
            'respondent_state_id'     => $respondentStateName ?? null,
            'respondent_city_id'      => $respondentCityName ?? null,
            'respondent_pincode'      => $row['respondent_pincode'] ?? null,
            'amount_in_dispute'       => $row['amount_in_dispute'] ?? null,
            'case_type'               => $row['case_type'] ?? null,
            'brief_of_case'           => $row['brief_of_case'] ?? null,
            'upload_evidence'         => $row['upload_evidence'] ?? null,
        ]);
    }
    
}
