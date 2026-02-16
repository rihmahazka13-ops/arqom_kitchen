<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jurnal extends Model
{
    // Nama tabel di database
    protected $table = 'jurnal';

    // Menggunakan kd_jurnal sebagai primary key (bukan id)
    protected $primaryKey = 'kd_jurnal'; 

    // Karena kd_jurnal adalah String (Contoh: JRN001), matikan auto-increment
    public $incrementing = false;       
    protected $keyType = 'string';      

    // Matikan timestamps karena tabel tidak memiliki kolom created_at/updated_at
    public $timestamps = false; 

    // Daftar kolom yang boleh diisi secara massal
    protected $fillable = [
        'kd_jurnal', 
        'tgl_jurnal', 
        'ket_jurnal',
        'reff_type', // Pastikan kolom ini sudah ditambahkan di Langkah 1
        'reff_id'    // Pastikan kolom ini sudah ditambahkan di Langkah 1
    ];

    /**
     * Relasi ke tabel detail (djurnal)
     * Menggunakan kd_jurnal sebagai penghubung
     */
    public function details(): HasMany
    {
        return $this->hasMany(Djurnal::class, 'kd_jurnal', 'kd_jurnal');
    }
}