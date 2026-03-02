<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class OtbPort extends Model
{
    protected $fillable = ['tower_otb_id', 'port_no', 'status', 'photo_path', 'note'];

    public function otb()
    {
        return $this->belongsTo(TowerOtb::class, 'tower_otb_id');
    }
}

