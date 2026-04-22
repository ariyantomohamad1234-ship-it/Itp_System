<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectTemplate extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function templateModuls()
    {
        return $this->hasMany(TemplateModul::class)->orderBy('sort_order');
    }

    /**
     * Get active templates for dropdown
     */
    public static function getActiveTemplates()
    {
        return static::where('is_active', true)->orderBy('name')->get();
    }
}
