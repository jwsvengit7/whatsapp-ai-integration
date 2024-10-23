<?php

namespace App\Models;

use App\Enums\Status;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        "phone",
        'myref',
        'image',
        'otp',
        'status',
        'link',
        'role',
        'otp_date',
        'link_expiration',
        'address'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'otp',
        'remember_token',
    ];



    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return $casts = [
        'email_verified_at' => 'datetime',
            'otp_date'=> 'datetime',
            'link_expiration'=>'datetime',
        'password' => 'hashed',
            'role' => UserRole::class,
            'status' => Status::class,
    ];
    }


    /**
     * Get the products for the users.
     */
    public function products(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function vendorProducts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(VendorProduct::class, 'vendor_id');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function setPasswordAttribute($password): void
    {
        if ( $password !== null & $password !== "" ) {
            $this->attributes['password'] = bcrypt($password);
        }
    }

}
