<?php

namespace App\Models;

use App\Traits\CustomScopes;
use App\Observers\DrpObserver;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Drp extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, CustomScopes;

    public static function boot()
    {
        parent::boot();
        self::observe(new DrpObserver);
    }

    protected $fillable = [
        'drp_type',
        'slug',
        'name',
        'email',
        'mobile',
        'status',
        'image',
        'signature_drp',
        'state_id',
        'city_id',
        'approve_status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    public function drpDetail()
    {
        return $this->hasOne(DrpDetail::class, 'drp_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    // protected function image(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn ($value) => Helper::showImage($value, true),
    //     );
    // }
}
