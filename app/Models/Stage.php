<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stage extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $fillable = [
        'file_case_id',
        'stage_type',
        'stage',
        'stage_date',
        'stage_send_date',
        'email_status',
        'whatsapp_status',
        'whatsapp_stage_status',
        'whatsapp_dispatch_datetime',
    ];


}
