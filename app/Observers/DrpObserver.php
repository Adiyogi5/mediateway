<?php

namespace App\Observers;

use App\Models\Drp;
use App\Helper\Helper;
use Illuminate\Support\Str;

class DrpObserver
{
    public function creating(Drp $drp)
    {
        $drp->slug      = Str::uuid();
        // $drp->image     = $drp->image ?? 'admin/avatar.png';
    }

    public function saving(Drp $drp)
    {
        $drp->slug      = Str::uuid();
        // $drp->image     = $drp->image ?? 'admin/avatar.png';
    }

    public function created(Drp $drp)
    {
        // Code after save
        $drp->drpId = Helper::orderId($drp->id, 'DRP', 6);
        $drp->saveQuietly();
    }
}
