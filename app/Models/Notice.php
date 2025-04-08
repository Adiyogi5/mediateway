<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notice extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $fillable = [
        'file_case_id',
        'notice_type',
        'notice',
        'notice_date',
    ];


}
