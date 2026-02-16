<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penjualan extends Model
{
    protected $table = 'penjualan';
    protected $primaryKey = 'kd_penj';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kd_penj', 
        'tgl_penj', 
        'nm_kons', 
        'tot_awal', 
        'tot_akhir', 
        'bud_pengiriman',
        'invoice'
    ];

    // ==================================================
    // RELASI PEMASUKAN (FIXED: PAKAI KODE PENJUALAN)
    // ==================================================
    public function pemasukans()
    {
        /**
         * Penjelasan: 
         * Kita hubungkan tabel pemasukans menggunakan 'kd_penj'.
         * Parameter 1: Model Pemasukan
         * Parameter 2: Foreign Key di tabel pemasukan (kd_penj)
         * Parameter 3: Local Key di tabel penjualan (kd_penj)
         */
        return $this->hasMany(Pemasukan::class, 'kd_penj', 'kd_penj');
    }

    public function konsumen()
    {
        return $this->belongsTo(Konsumen::class, 'kd_kons');
    }
}