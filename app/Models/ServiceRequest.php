<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'service_category_id',
        'description',
        'date',
        'time',
        'status',
        'request_picture',
        'location',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function serviceProviders()
    {
        return $this->belongsToMany(User::class, 'service_provider_services');
    }

    public function serviceProvider()
    {
        return $this->belongsTo(User::class, 'service_provider_id');
    }
}
