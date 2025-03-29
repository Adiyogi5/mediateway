<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileCasePayment extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $fillable = [
        'file_case_id', 'file_case_no', 'name', 'mobile','email', 'message','transaction_id','payment_status','payment_date','payment_amount'
    ];

    public function fileCase()
    {
        return $this->belongsTo(FileCase::class, 'file_case_id');
    }
}
