<?php

namespace App\Models;

use App\Traits\CustomScopes;
use App\Observers\OrganizationObserver;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Organization extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, CustomScopes;

    public static function boot()
    {
        parent::boot();
        self::observe(new OrganizationObserver);
    }

    protected $fillable = [
        'parent_id',
        'slug',
        'organization_role_id',
        'name',
        'email',
        'mobile',
        'status',
        'image',
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

    public function organizationDetail()
    {
        return $this->hasOne(organizationDetail::class, 'organization_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function organization_permission(): HasMany
    {
        return $this->hasMany(OrganizationPermission::class, 'organization_id');
    }

    // protected function image(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn ($value) => Helper::showImage($value, true),
    //     );
    // }
}
