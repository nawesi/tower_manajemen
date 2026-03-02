<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TowerImage extends Model
{
    protected $fillable = [
        'tower_id',
        'stack_no',
        'side',
        'image_path',
    ];

    protected $casts = [
        'tower_id' => 'integer',
        'stack_no' => 'integer',
        'side'     => 'integer',
    ];
}
