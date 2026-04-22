<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateBlok extends Model
{
    protected $fillable = ['template_modul_id', 'nama_blok', 'sort_order'];

    public function templateModul()
    {
        return $this->belongsTo(TemplateModul::class);
    }

    public function templateSubBloks()
    {
        return $this->hasMany(TemplateSubBlok::class)->orderBy('sort_order');
    }
}
