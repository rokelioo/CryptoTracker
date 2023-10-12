<?php

namespace App\Services;

use App\Models\CryptoCurrency;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class TransformCryptoAddPercentage
{
    public function addSupplyPercentageToCryptos(LengthAwarePaginator $cryptos): LengthAwarePaginator
    {
        $cryptos->getCollection()->transform(function ($crypto) {
            $crypto->percentage = ($crypto['max_supply'] != 0) 
                ? $crypto['circulating_supply'] * 100 / $crypto['max_supply'] 
                : -1;
            return $crypto;
        });
        return  $cryptos;
    }
}