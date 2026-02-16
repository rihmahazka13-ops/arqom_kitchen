<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barangjadi extends Model
{
    use HasFactory;

    protected $table = 'barangjadi';

    // 1. KUNCI PERUBAHAN: Ubah primary key ke 'id' (Auto Increment)
    // Agar satu kode barang bisa memiliki banyak baris (per proyek/per batch)
    protected $primaryKey = 'id'; 
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = false;

    protected $fillable = [
        'kd_brgjadi',
        'nm_brgjadi',
        'nm_kons',      // 2. WAJIB TAMBAH: Agar bisa simpan nama proyek/konsumen
        'jmlh_brgjadi',
        'hpp_unit',
        'hpp_total'
    ];
}