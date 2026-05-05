<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id', 'type', 'title', 'message', 'link',
        'related_itp_id', 'related_project_id', 'sender_id', 'is_read'
    ];

    protected $casts = ['is_read' => 'boolean'];

    public function user() { return $this->belongsTo(User::class); }
    public function sender() { return $this->belongsTo(User::class, 'sender_id'); }
    public function itp() { return $this->belongsTo(Itp::class, 'related_itp_id'); }
    public function project() { return $this->belongsTo(Project::class, 'related_project_id'); }
}
