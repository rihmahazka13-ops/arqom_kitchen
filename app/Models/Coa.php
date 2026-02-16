<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coa extends Model
{
    use HasFactory;

    // 1. Tentukan Nama Tabel (Sesuai database)
    protected $table = 'coa';

    // 2. Tentukan Primary Key (Bukan 'id')
    protected $primaryKey = 'kd_akun';

    // 3. Matikan Auto Increment (PK adalah String)
    public $incrementing = false;

    // 4. Tentukan Tipe Data Primary Key
    protected $keyType = 'string';

    // 5. Matikan Timestamps (Jika tidak ada kolom created_at/updated_at)
    public $timestamps = false; 

    // 6. Daftar Kolom yang Boleh Diisi
    protected $fillable = [
        'kd_akun',
        'nama_akun',
        'jenis_akun'
    ];

    /**
     * RELASI KE DETAIL JURNAL (djurnal)
     * Sangat penting untuk fitur Buku Besar agar bisa menarik riwayat transaksi.
     */
    public function jurnals()
    {
        // Menghubungkan ke model Djurnal (tabel djurnal)
        // Parameter: (Model tujuan, Foreign Key di djurnal, Local Key di coa)
        return $this->hasMany(Djurnal::class, 'kd_akun', 'kd_akun');
    }

    /**
     * SCOPE ATAU ACCESSOR UNTUK SALDO (Opsional tapi membantu)
     * Memudahkan pengecekan apakah akun ini saldo normalnya Debit atau Kredit.
     */
    public function isDebitAccount()
    {
        $jenis = strtoupper($this->jenis_akun);
        $debitGroup = ['ASET', 'AKTIVA', 'BEBAN', 'BEBAN COGS', 'BEBAN OPERASIONAL'];
        
        foreach ($debitGroup as $dg) {
            if (str_contains($jenis, $dg)) {
                return true;
            }
        }
        return false;
    }
}