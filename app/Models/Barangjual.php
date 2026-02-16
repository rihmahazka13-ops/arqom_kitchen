<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangJual extends Model
{
    protected $table = 'barangjual';
    protected $primaryKey = 'kd_brgjual';
    public $incrementing = false; // Karena kodenya string (BJ-0001)
    protected $keyType = 'string';

    protected $fillable = ['kd_brgjual', 'nm_brgjual'];
    
    // Matikan timestamps jika tabelmu tidak punya kolom created_at/updated_at
    public $timestamps = false; 
}
