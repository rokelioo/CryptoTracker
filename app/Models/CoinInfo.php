<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoinInfo extends Model
{
    protected $table = 'coininfo'; 

    protected $primaryKey = 'Pk_ID'; 

    public $timestamps = false;

    protected $fillable = [
        'name',
        'symbol',
        'rank',
        'price_usd',
        'market_cap_usd',
        'volume_usd_24h',
        'circulating_supply',
        'total_supply',
        'max_supply',
        'percent_change_1h',
        'percent_change_24h',
        'percent_change_7d',
        'last_updated'
    ];

    public function coinHistories()
    {
        return $this->hasMany(CoinHistory::class, 'Fk_coininfo', 'Pk_ID');
    }
}
