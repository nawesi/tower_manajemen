<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CableMap extends Model
{
    protected $fillable = ['name', 'kml_path', 'is_active', 'notes'];
}