<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
{
    protected $fillable = [
        'project_id',
        'itp_id',
        'altitude',
        'roll',
        'laser_distance',
        'status'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function itp()
    {
        return $this->belongsTo(Itp::class);
    }
}
