<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubBlok extends Model
{
    protected $fillable = ['blok_id', 'nama_sub_blok'];

    public function blok()
    {
        return $this->belongsTo(Blok::class);
    }

    public function itps()
    {
        return $this->hasMany(Itp::class);
    }
}
