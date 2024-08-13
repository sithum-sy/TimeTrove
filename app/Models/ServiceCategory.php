<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'created_by',
        'is_active',
    ];

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'service_category_id');
    }
}
