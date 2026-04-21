<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blok extends Model
{
    protected $fillable = ['modul_id', 'nama_blok'];

    public function modul()
    {
        return $this->belongsTo(Modul::class);
    }

    public function subBloks()
    {
        return $this->hasMany(SubBlok::class);
    }
}
