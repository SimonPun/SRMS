<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'notifications_last_read_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function assignedRequests()
    {
        return $this->belongsToMany(ServiceRequest::class, 'service_request_staff', 'user_id', 'service_request_id')
            ->withPivot('staff_status')
            ->withTimestamps();
    }

    public function requestUpdates()
    {
        return $this->hasMany(RequestUpdate::class, 'updated_by');
    }

    public function dismissedRequestUpdates()
    {
        return $this->belongsToMany(RequestUpdate::class, 'request_update_user_dismissals', 'user_id', 'request_update_id')
            ->withTimestamps();
    }
}
