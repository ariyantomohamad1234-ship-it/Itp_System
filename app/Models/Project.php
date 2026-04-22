<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = ['nama_project', 'kode_project', 'deskripsi', 'status', 'template_id', 'tanggal_kontrak', 'tanggal_mulai', 'deadline'];

    protected $casts = [
        'tanggal_kontrak' => 'date',
        'tanggal_mulai' => 'date',
        'deadline' => 'date',
    ];

    public function template()
    {
        return $this->belongsTo(ProjectTemplate::class, 'template_id');
    }

    public function moduls()
    {
        return $this->hasMany(Modul::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_user')->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
