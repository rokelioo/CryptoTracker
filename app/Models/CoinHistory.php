<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoinHistory extends Model
{
    protected $table = 'coinhistory';

    protected $primaryKey = 'Pk_ID'; 

    public $timestamps = false; 

    protected $fillable = [
        'Fk_coininfo',
        'price_usd',
        'volume_usd_24h',
        'timestamp'
    ];

    public function coinInfo()
    {
        return $this->belongsTo(CoinInfo::class, 'Fk_coininfo', 'Pk_ID');
    }
}
