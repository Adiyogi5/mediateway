<?php

namespace App\Observers;

use App\Models\Individual;
use App\Helper\Helper;
use Illuminate\Support\Str;

class IndividualObserver
{
    public function creating(Individual $individual)
    {
        $individual->slug      = Str::uuid();
        // $individual->image     = $individual->image ?? 'admin/avatar.png';
    }

    public function saving(Individual $individual)
    {
        $individual->slug      = Str::uuid();
        // $individual->image     = $individual->image ?? 'admin/avatar.png';
    }

    public function created(Individual $individual)
    {
        // Code after save
        $individual->individualId = Helper::orderId($individual->id, 'IND', 6);
        $individual->saveQuietly();
    }
}
