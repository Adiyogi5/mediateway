<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssignCase extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $fillable = [
        'case_id',
        'arbitrator_id',
        'advocate_id',
        'case_manager_id',
        'mediator_id',
        'conciliator_id',
    ];


}
