<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProviderServices extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_provider_id',
        'service_category_id',
        'description',
        'availability',
        'rate',
        'city',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'service_provider_id', 'user_id');
    }
}
