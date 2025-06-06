<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\SoftDeletes;

class IndividualDetail extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $fillable = [
        'individual_id', 'university', 'degree','year', 'adhar_card',
    ];

    public function individual()
    {
        return $this->belongsTo(Individual::class, 'individual_id');
    }
}
