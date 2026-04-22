<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateModul extends Model
{
    protected $fillable = ['project_template_id', 'nama_modul', 'deskripsi', 'sort_order', 'start_day', 'duration_days'];

    public function template()
    {
        return $this->belongsTo(ProjectTemplate::class, 'project_template_id');
    }

    public function templateBloks()
    {
        return $this->hasMany(TemplateBlok::class)->orderBy('sort_order');
    }
}
