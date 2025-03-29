<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderSheet extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $fillable = [
        'drp_type', 'name','subject', 'email_content', 'notice_format', 'status',
    ];

}
