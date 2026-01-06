<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LokasiUnpam extends Model
{
    protected $table = 'lokasi_unpam';

    protected $fillable = [
        'nama_lokasi',
        'latitude',
        'longitude',
        'radius_meter',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
