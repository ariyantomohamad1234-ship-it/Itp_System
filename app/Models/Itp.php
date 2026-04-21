<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Itp extends Model
{
    protected $fillable = [
        'sub_blok_id', 'assembly_code', 'assembly_description', 'code', 'item',
        'yard_val', 'class_val', 'os_val', 'stat_val'
    ];

    public function subBlok()
    {
        return $this->belongsTo(SubBlok::class);
    }

    public function itpData()
    {
        return $this->hasMany(ItpData::class);
    }

    /**
     * Mendapatkan nilai partisipasi untuk role tertentu
     */
    public function getValForRole(string $role): string
    {
        return match ($role) {
            'yard'  => $this->yard_val,
            'class' => $this->class_val,
            'os'    => $this->os_val,
            'stat'  => $this->stat_val,
            default => '-',
        };
    }

    /**
     * Semua item selalu visible untuk semua role.
     * Perbedaannya hanya di tombol submit (W/RV bisa submit, -/NA tidak).
     */
    public function isVisibleForRole(string $role): bool
    {
        return true;
    }

    /**
     * Cek apakah role ini boleh submit data ITP (hanya W atau RV)
     */
    public function canSubmitForRole(string $role): bool
    {
        $val = $this->getValForRole($role);
        return in_array(strtoupper($val), ['W', 'RV']);
    }

    /**
     * Cek apakah foto wajib untuk role ini (W = Witness)
     */
    public function isPhotoRequired(string $role): bool
    {
        return strtoupper($this->getValForRole($role)) === 'W';
    }
}
