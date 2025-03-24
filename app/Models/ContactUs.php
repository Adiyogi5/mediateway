<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomScopes;

class ContactUs extends Model
{
    use HasFactory, CustomScopes;

    protected $fillable = [
        'first_name',
        'last_name',
        'mobile',
        'email',
        'subject',
        'message',
    ];
}
