<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstallationRequest extends Model
{
    protected $fillable = [
        'user_id',
        'tower_id',
        'vendor_department',
        'device_name',
        'stack_no',
        'height_from_ground_m',
        'device_photo_path',
        'status',
        'admin_comment',
        'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function tower()
    {
        return $this->belongsTo(Tower::class);
    }
}
