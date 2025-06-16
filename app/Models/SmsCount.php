<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmsCount extends Model
{
    use HasFactory;

    protected $casts = [
    'count' => 'integer',
];

    public $timestamps = false;

    protected $fillable = [
        'credited', 'count',
    ];


}
