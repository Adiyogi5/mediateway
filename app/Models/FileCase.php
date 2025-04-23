<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FileCase extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $fillable = [
        'user_type',
        'individual_id',
        'organization_id',
        'case_number',
        'loan_number',
        'agreement_date',
        'loan_application_date',
        'arbitration_date',
        'claimant_first_name',
        'claimant_middle_name',
        'claimant_last_name',
        'claimant_mobile',
        'claimant_email',
        'claimant_address1',
        'claimant_address2',
        'claimant_address_type',
        'claimant_state_id',
        'claimant_city_id',
        'claimant_pincode',
        'respondent_first_name',
        'respondent_middle_name',
        'respondent_last_name',
        'respondent_mobile',
        'respondent_email',
        'respondent_address1',
        'respondent_address2',
        'respondent_address_type',
        'respondent_state_id',
        'respondent_city_id',
        'respondent_pincode',
        'brief_of_case',
        'amount_in_dispute',
        'case_type',
        'language',
        'agreement_exist',
        'application_form',
        'foreclosure_statement',
        'loan_agreement',
        'account_statement',
        'other_document',
        'status',
    ];

    public function file_case_details(): HasOne
    {
        return $this->hasOne(FileCaseDetail::class);
    }

    public function guarantors(): HasOne
    {
        return $this->hasOne(Guarantor::class);
    }

    public function individual()
    {
        return $this->belongsTo(Individual::class, 'individual_id');
    }

    public function payments()
    {
        return $this->hasMany(FileCasePayment::class, 'file_case_id');
    }

    public function assignedCases()
    {
        return $this->hasMany(AssignCase::class, 'case_id');
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
