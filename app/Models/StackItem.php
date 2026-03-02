<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StackItem extends Model
{
    protected $fillable = ['tower_id', 'stack_no', 'device_name'];

    public function tower()
    {
        return $this->belongsTo(Tower::class);
    }
}
