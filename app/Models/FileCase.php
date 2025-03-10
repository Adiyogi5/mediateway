<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileCase extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $fillable = [
        'individual_id',
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
        'add_respondent',
        'brief_of_case',
        'amount_in_dispute',
        'case_type',
        'language',
        'agreement_exist',
        'upload_evidence',
    ];


    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
