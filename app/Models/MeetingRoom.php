<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeetingRoom extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $fillable = [
        'room_id',
        'meeting_room_case_id',
        'conciliator_id',
        'date',
        'time',
        'status',
        'recording_url',
        'send_mail_to_respondent',
        'email_send_date',
        'send_whatsapp_to_respondent',
        'whatsapp_dispatch_datetime'
    ];
}
