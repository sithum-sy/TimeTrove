<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    const USER_ROLE_ADMIN = 'admin';
    const USER_ROLE_SCHEDULER = 'scheduler';
    const USER_ROLE_CLIENT = 'client';
    const USER_ROLE_SERVICE_PROVIDER = 'service_provider';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'role',
        'email',
        'phone_number',
        'date_of_birth',
        'address',
        'gender',
        'password',
        'created_by',
        'is_active',
        'is_default_password_changed',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function serviceProviderServices()
    {
        return $this->hasMany(ServiceProviderServices::class, 'service_provider_id');
    }

    public function serviceRequests()
    {
        return $this->belongsToMany(ServiceRequest::class, 'service_provider_services');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'service_provider_id');
    }

    public function averageRating()
    {
        return $this->ratings()->avg('rating') ?? 0;
    }

    public function ratingCount()
    {
        return $this->ratings()->count();
    }

    public function serviceRequestsForServiceProviders()
    {
        return $this->hasMany(ServiceRequest::class, 'service_provider_id');
    }
}
