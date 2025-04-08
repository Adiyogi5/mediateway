<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileCaseDetail extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $fillable = [
        'file_case_id',
        'product',
        'asset_description',
        'registration_no',
        'chassis_no',
        'engin_no',
        'finance_amount',
        'finance_amount_in_words',
        'emi_amt',
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
    ];


}
