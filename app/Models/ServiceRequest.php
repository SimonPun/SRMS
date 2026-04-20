<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceRequest extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'category',
        'location',
        'priority',
        'status',
        'assigned_to',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedStaff()
    {
        return $this->belongsToMany(User::class, 'service_request_staff', 'service_request_id', 'user_id')
            ->withPivot('staff_status')
            ->withTimestamps();
    }

    public function updates()
    {
        return $this->hasMany(RequestUpdate::class)->latest();
    }
}
