<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CryptoCurrency extends Model
{
    protected $fillable = [
        'name',
        'symbol',
        "slug",
        'rank',
        'price',
        'volume_24h',
        'market_cap',
        'circulating_supply',
        'total_supply',
        'max_supply',
        'percent_change_1h',
        'percent_change_24h',
        'percent_change_7d',
        'last_updated'
    ];

    public function cryptoHistory() :HasMany
    {
        return $this->hasMany(CryptoHistory::class);
    }
}
