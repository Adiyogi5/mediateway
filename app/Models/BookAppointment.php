<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookAppointment extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'book_appointments';
    
    protected $fillable = [
        'name',
        'mobile',
        'email',
        'datestart',
        'dateend',
    ];

}
