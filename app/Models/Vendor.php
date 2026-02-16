<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Vendor extends Model
{
    protected $table = 'vendor';
    protected $primaryKey = 'kd_vendor';
    public $incrementing = false;
    public $timestamps  = false;
    protected $fillable = [
        'kd_vendor',
        'nm_vendor'
        
    ];

    protected $keyType = 'string';
}
