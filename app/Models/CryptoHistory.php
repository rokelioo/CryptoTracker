<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CryptoHistory extends Model
{
    protected $fillable = [
        'crypto_currency_id',
        'price',
        'volume_24h'
    ];
    
    public function cryptoCurency(): BelongsTo
    {
        return $this->belongsTo(CryptoCurrency::class);
    }
}
