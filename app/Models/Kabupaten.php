<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kabupaten extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_kabupaten',
        'kode_kabupaten',
        'jumlah_anggota',
        'status',
    ];

    protected $casts = [
        'jumlah_anggota' => 'integer',
    ];

    /**
     * Get the users for the kabupaten.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'kabupaten_id');
    }

    /**
     * Get the iurans for the kabupaten.
     */
    public function iurans()
    {
        return $this->hasMany(Iuran::class, 'kabupaten_id');
    }
}
