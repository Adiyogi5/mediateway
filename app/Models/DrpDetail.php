<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\SoftDeletes;

class DrpDetail extends Model
{
    use HasFactory, SoftDeletes, CustomScopes;

    protected $fillable = [
        'drp_id', 'university', 'field_of_study', 'degree','year', 'description', 'achievement_od_socities', 'designation', 'organization', 'professional_degree', 'registration_no', 'job_description', 'currently_working_here', 'years_of_experience', 'registration_certificate', 'attach_registration_certificate', 'experience_in_the_field_of_drp', 'areas_of_expertise', 'membership_of_professional_organisation', 'no_of_awards_as_arbitrator', 'total_years_of_working_as_drp', 'functional_area_of_drp',
    ];
}
