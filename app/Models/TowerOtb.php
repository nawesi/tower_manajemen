<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TowerOtb extends Model
{
    protected $fillable = ['tower_id', 'name', 'total_ports'];

    public function tower()
    {
        return $this->belongsTo(Tower::class);
    }

    public function ports()
    {
        return $this->hasMany(OtbPort::class, 'tower_otb_id');
    }
}