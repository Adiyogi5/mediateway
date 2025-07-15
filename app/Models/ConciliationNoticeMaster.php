<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConciliationNoticeMaster extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $fillable = [
        'case_manager_id',
        'conciliation_notice_type',
        'file_name',
        'uploaded_by',
        'date',
    ];

}
