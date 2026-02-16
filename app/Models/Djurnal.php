<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Djurnal extends Model
{
    // Nama tabel sesuai database arqom_kitchen
    protected $table = 'djurnal';

    // Tabel djurnal menggunakan kolom 'id' sebagai primary key
    protected $primaryKey = 'id';

    // Matikan timestamps karena tabel detail tidak memiliki kolom created_at/updated_at
    public $timestamps = false; 

    // Daftar kolom yang diizinkan untuk diisi secara massal
    protected $fillable = [
        'kd_jurnal', 
        'kd_akun', 
        'debit', 
        'kredit'
    ];

    /**
     * Relasi balik ke header Jurnal
     * Menghubungkan detail jurnal ke header-nya melalui kd_jurnal
     */
    public function jurnal(): BelongsTo
    {
        return $this->belongsTo(Jurnal::class, 'kd_jurnal', 'kd_jurnal');
    }

    /**
     * Relasi ke Master Akun (COA)
     * Sangat penting agar bisa mengambil data saldo berdasarkan kd_akun
     */
    public function coa(): BelongsTo
    {
        return $this->belongsTo(Coa::class, 'kd_akun', 'kd_akun');
    }
}