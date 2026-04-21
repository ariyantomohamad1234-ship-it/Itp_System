<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Modul extends Model
{
    protected $fillable = ['project_id', 'nama_modul', 'deskripsi'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function bloks()
    {
        return $this->hasMany(Blok::class);
    }
}
