<?php

namespace App\Interfaces;

use App\Models\CryptoCurrency;
use App\Models\CryptoHistory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CryptoRepositoryInterface
{
    public function createOrUpdateCrypto(array $cryptoData): CryptoCurrency;
    public function createCryptoHistory(CryptoCurrency $cryptoData): CryptoHistory;
    public function getCryptosOrderedBy(string $orderByColumn, int $paginationCount): LengthAwarePaginator;
    public function findCryptoById(int $id): CryptoCurrency;
    public function searchCryptosByTerm(string $nameColumn, string $term, string $orderByColumn, int $paginationCount): LengthAwarePaginator;
}