<?php

namespace App\Repositories;

use App\Interfaces\CryptoRepositoryInterface;
use App\Models\CryptoCurrency;
use App\Models\CryptoHistory;
use DateTime;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CryptoRepository implements CryptoRepositoryInterface
{
    /**
     * Create or update a crypto entry in the database.
     *
     * @param array $cryptoData The data of the crypto to be stored.
     * @return CryptoCurrency The created or updated crypto entry.
     */
    public function createOrUpdateCrypto(array $cryptoData): CryptoCurrency
    {
        return CryptoCurrency::updateOrCreate(
            ['name' => $cryptoData['name']],
            $this->prepareCryptoData($cryptoData)
        );
    }

     /**
     * Create a history entry for a given crypto.
     *
     * @param CryptoCurrency $cryptoData The crypto for which the history will be created.
     * @return CryptoHistory The created history entry.
     */
    public function createCryptoHistory(CryptoCurrency $cryptoData): CryptoHistory
    {
        return CryptoHistory::create($this->prepareCryptoHistory($cryptoData));
    }

    /**
     * Get cryptos ordered by a specific column with pagination.
     *
     * @param string $orderByColumn The column to order by.
     * @param int $paginationCount Number of entries per page.
     * @return LengthAwarePaginator The paginated results.
     */
    public function getCryptosOrderedBy(string $orderByColumn, int $paginationCount): LengthAwarePaginator
    {
        return CryptoCurrency::orderBy($orderByColumn)->paginate($paginationCount);
    }

    /**
     * Find a crypto by its ID.
     *
     * @param int $id The ID of the crypto to find.
     * @return CryptoCurrency The found crypto entry.
     */
    public function findCryptoById(int $id): CryptoCurrency
    {
        return CryptoCurrency::findOrFail($id);
    }

    /**
     * Search cryptos by a term, order them by a column, and paginate results.
     *
     * @param string $nameColumn The column to search against.
     * @param string $term The term to search for.
     * @param string $orderByColumn The column to order by.
     * @param int $paginationCount Number of entries per page.
     * @return LengthAwarePaginator The paginated results.
     */
    public function searchCryptosByTerm(string $nameColumn, string $term, string $orderByColumn, int $paginationCount): LengthAwarePaginator
    {
        return CryptoCurrency::where($nameColumn, 'LIKE', "%{$term}%")
        ->orderBy($orderByColumn)
        ->paginate($paginationCount);;
    }

    /**
     * Prepare the data for storing a crypto.
     *
     * @param array $crypto The crypto data.
     * @return array The prepared data.
     */
    private function prepareCryptoData(array $crypto): array
    {
        return [
            'symbol' => $crypto['symbol'],
            'slug' => $crypto['slug'],
            'rank' => $crypto['cmc_rank'],
            'price' => $crypto['quote']['USD']['price'],
            'market_cap' => round($crypto['quote']['USD']['market_cap'], 2),
            'volume_24h' => round($crypto['quote']['USD']['volume_24h'], 2),
            'circulating_supply' => round($crypto['circulating_supply'], 2),
            'total_supply' => round($crypto['total_supply'], 2),
            'max_supply' => round($crypto['max_supply'], 2),
            'percent_change_1h' => round($crypto['quote']['USD']['percent_change_1h'], 2),
            'percent_change_24h' => round($crypto['quote']['USD']['percent_change_24h'], 2),
            'percent_change_7d' => round($crypto['quote']['USD']['percent_change_7d'], 2),
            'last_updated' => (new DateTime($crypto['quote']['USD']['last_updated']))->format('Y-m-d H:i:s')
        ];
    }

    /**
     * Prepare the history data for a crypto.
     *
     * @param CryptoCurrency $crypto The crypto data.
     * @return array The prepared history data.
     */
    private function prepareCryptoHistory(CryptoCurrency $crypto): array
    {
        return [
            'crypto_currency_id' => $crypto->id,
            'price' => $crypto->price,
            'volume_24h' => $crypto->volume_24h
        ];
    }

}