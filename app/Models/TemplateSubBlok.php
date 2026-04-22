<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateSubBlok extends Model
{
    protected $fillable = ['template_blok_id', 'nama_sub_blok', 'sort_order'];

    public function templateBlok()
    {
        return $this->belongsTo(TemplateBlok::class);
    }

    public function templateItps()
    {
        return $this->hasMany(TemplateItp::class);
    }
}
