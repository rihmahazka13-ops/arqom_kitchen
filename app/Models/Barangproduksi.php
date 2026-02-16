<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barangproduksi extends Model
{
    protected $table = 'barangproduksi';
    protected $primaryKey = 'kd_bhnpro';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kd_bhnpro', 
        'nm_kons',
        'bhnpro',
        'est_hpp',
        'jml_pro', 
        'tot_est', 
        'estrl_hpp',
        'selisih',
        'rl_hpp'
    ];

        public function konsumen()
    {
        return $this->belongsTo(Konsumen::class, 'kd_kons');
    }


    // ✅ Tambahkan ini untuk mematikan created_at & updated_at
    public $timestamps = false;
}