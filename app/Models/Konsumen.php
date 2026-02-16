<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Konsumen extends Model
{
    protected $table = 'konsumen';
    protected $primaryKey = 'kd_kons';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'kd_kons',
        'nm_kons',
        'kontak_kons',
        'kota_kons'
    ];
    public $timestamps = false;
}
	
