<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bahanbaku extends Model
{
    protected $table = 'bahanbaku';
    protected $primaryKey = 'kd_bhn';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kd_bhn', 
        'nm_kons',
        'nm_bhn',
        'jml_bhn', 
        'satuan_bhn', 
        'harga_bhn',
        'tot_bhn'
    ];

    public function konsumen()
    {
        return $this->belongsTo(Konsumen::class, 'kd_kons');
    }

    // ✅ Tambahkan ini untuk mematikan created_at & updated_at
    public $timestamps = false;
}