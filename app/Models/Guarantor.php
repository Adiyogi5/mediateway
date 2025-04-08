<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guarantor extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $fillable = [
        'file_case_id',
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

}
