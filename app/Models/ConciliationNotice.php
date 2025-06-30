<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConciliationNotice extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $fillable = [
        'file_case_id',
        'conciliation_notice_type',
        'notice_copy',
        'notice_date',
        'notice_send_date',
        'email_status',
        'whatsapp_status',
        'whatsapp_notice_status',
        'whatsapp_dispatch_datetime',
    ];


    public function fileCase()
    {
        return $this->belongsTo(FileCase::class, 'file_case_id');
    }

}
