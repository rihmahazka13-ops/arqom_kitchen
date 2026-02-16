<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    // Nama tabel sesuai di phpMyAdmin
    protected $table = 'pengeluaran';

    // Primary key
    protected $primaryKey = 'kd_peng';

    // Karena kode string (PJ-001) dan input manual
    public $incrementing = false;
    protected $keyType = 'string';
    

    // Kolom yang diizinkan untuk diisi (Mass Assignment)
    protected $fillable = [
        'kd_peng',
        'tgl_peng',
        'kategori',      // Operasional / Bahan Baku / COGS Lainnya
        'nm_kons',
        'nm_vendor',
        'spek_peng',
        'nm_bhn',
        'jml_peng',
        'satuan_peng',
        'hrg_peng',
        'tot_peng',      // Total Harga
        'buktinot_peng',
        'buktitf_peng'
    ];

    // Matikan timestamps
    public $timestamps = false;

    // --- RELASI ---

    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'kd_vendor');
    }

    /**
     * Relasi ke Tabel Jurnal Umum
     * Menghubungkan kd_peng (Pengeluaran) dengan reff_id (Jurnal)
     */
    public function jurnal()
    {
        return $this->hasOne(Jurnal::class, 'reff_id', 'kd_peng')
                    ->where('reff_type', 'Pengeluaran');
    }
}