<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestUpdate extends Model
{
    protected $fillable = [
        'service_request_id',
        'updated_by',
        'old_status',
        'new_status',
        'note',
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function dismissedBy()
    {
        return $this->belongsToMany(User::class, 'request_update_user_dismissals', 'request_update_id', 'user_id')
            ->withTimestamps();
    }
}
