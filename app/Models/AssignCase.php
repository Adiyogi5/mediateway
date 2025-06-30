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
        'sendto_casemanager',
        'receiveto_casemanager',
        'is_assigned',
    ];

    public function fileCase()
    {
        return $this->belongsTo(FileCase::class, 'case_id');
    }

    public function arbitrator()
    {
        return $this->belongsTo(Drp::class, 'arbitrator_id', 'id');
    }

    public function advocate()
    {
        return $this->belongsTo(Drp::class, 'advocate_id', 'id');
    }

    public function caseManager()
    {
        return $this->belongsTo(Drp::class, 'case_manager_id', 'id');
    }

    public function mediator()
    {
        return $this->belongsTo(Drp::class, 'mediator_id', 'id');
    }

    public function conciliator()
    {
        return $this->belongsTo(Drp::class, 'conciliator_id', 'id');
    }

}
