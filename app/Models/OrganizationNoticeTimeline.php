<?php

namespace App\Models;

use App\Traits\CustomScopes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationNoticeTimeline extends Model
{
    use HasFactory, CustomScopes;

    protected $fillable = [
        'organization_list_id',
        'notice_1',
        'notice_1a',
        'notice_1b',
        'notice_2b',
        'notice_3a',
        'notice_3b',
        'notice_3c',
        'notice_3d',
        'notice_4a',
        'notice_5a',
        'notice_second_hearing',
        'notice_final_hearing',
    ];

}
