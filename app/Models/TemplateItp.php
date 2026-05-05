<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TemplateItp extends Model
{
    protected $fillable = [
        'template_sub_blok_id', 'assembly_code', 'assembly_description',
        'code', 'item', 'yard_val', 'class_val', 'os_val', 'stat_val',
    ];

    public function templateSubBlok()
    {
        return $this->belongsTo(TemplateSubBlok::class);
    }
}
