<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemasukan extends Model
{
    // Nama tabel sesuai di phpMyAdmin kamu
    protected $table = 'pemasukan';

    // Primary key menggunakan kode pemasukan
    protected $primaryKey = 'kd_pem';

    // Karena kode biasanya string (IN-001) dan diinput manual
    public $incrementing = false;
    protected $keyType = 'string';

    // Kolom yang diizinkan untuk diisi (Mass Assignment)
    protected $fillable = [
    'kd_pem', 
    'kd_penj', // Tambahkan ini
    'tgl_pem', 
    'nm_kons', 
    'jml_pem', 
    'ket_pem', 
    'buktitf_pem'
    ];

    // =========================================================
    // UPDATE RELASI: HUBUNGKAN BERDASARKAN NAMA KONSUMEN
    // =========================================================
    public function penjualan()
    {
        // Artinya: Pemasukan ini milik Penjualan yang namanya sama
        return $this->belongsTo(Penjualan::class, 'nm_kons', 'nm_kons');
    }

    // Relasi ke Konsumen (Jika diperlukan)
    public function konsumen()
    {
        return $this->belongsTo(Konsumen::class, 'kd_kons');
    }

    // Matikan timestamps jika tabelmu tidak punya kolom created_at & updated_at
    public $timestamps = false;
}