<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItpData extends Model
{
    protected $table = 'itp_data';

    protected $fillable = ['itp_id', 'uploaded_by', 'photo', 'keterangan', 'status', 'approved_at', 'rejection_note'];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function itp()
    {
        return $this->belongsTo(Itp::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
