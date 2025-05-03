<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourtRoom extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $fillable = [
        'room_id',
        'case_id',
        'individual_id',
        'organization_id',
        'case_number',
        'arbitrator_id',
        'date',
        'time',
        'status',
    ];
}
