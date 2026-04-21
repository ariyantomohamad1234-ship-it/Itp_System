<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'username', 'password', 'role'];

    protected $hidden = ['password'];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isYard(): bool
    {
        return $this->role === 'yard';
    }

    public function isClass(): bool
    {
        return $this->role === 'class';
    }

    public function isOs(): bool
    {
        return $this->role === 'os';
    }

    public function isStat(): bool
    {
        return $this->role === 'stat';
    }

    public function itpData()
    {
        return $this->hasMany(ItpData::class, 'uploaded_by');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_user')->withTimestamps();
    }
}
