<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceFee extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $fillable = [
        'ticket_size_min',
        'ticket_size_max',
        'cost',
        'status',
    ];
}
