<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourtroomHearingLink extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $fillable = [
        'file_case_id',
        'hearing_type',
        'link',
        'date',
        'time',
        'email_status',
        'email_send_date',
        'whatsapp_status',
        'whatsapp_send_date',
        'sms_status',
        'sms_send_date',
    ];

}
