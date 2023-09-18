<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\CoinInfo;
use App\Models\CoinHistory;

class CoinMarketCapData extends Command
{
    protected $signature = 'coinmarketcap:fetch';
    protected $description = 'Fetch data from the CoinMarketCap API';
    protected $client;

    public function __construct(Client $client)
    {
        parent::__construct();
        $this->client = $client;
    }

    public function handle()
    {
            try {
                $data = $this->fetchDataFromAPI();

                $validator = $this->validateData($data);

                if ($validator->fails()) {
                    throw new ValidationException($validator);
                }
    
                $this->processData($data);
    
                $this->info('Data fetched successfully.');
    
            } catch (\Exception $e) {
                $this->handleException($e);
            }
    }
    protected function fetchDataFromAPI()
    {
        $parameters = [
            'start' => '1',
            'limit' => '550',
            'convert' => 'USD'
          ];

        $response = $this->client->get('https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest', [
            'query' => $parameters,
            'headers' => [
                'Accepts' => 'application/json',
                'X-CMC_PRO_API_KEY' => 'c0779ea5-606d-4bb5-acee-ce01429b750b',
            ],
        ]);

        return json_decode($response->getBody(), true);
    }
    /**
     * Validates the given data against the defined rules.
     *
     * @param array $data The data to validate.
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validateData($data)
    {
        $validator = Validator::make($data, [
            'status.error_code' => 'required|integer',
            'data.*.name' => 'required|string|max:255',
            'data.*.symbol' => 'required|string|max:10',
            'data.*.cmc_rank' => 'required|integer',
            'data.*.quote.USD.price' => 'required|numeric',
            'data.*.quote.USD.market_cap' => 'required|numeric',
            'data.*.quote.USD.volume_24h' => 'required|numeric',
            'data.*.circulating_supply' => 'required|numeric',
            'data.*.total_supply' => 'required|numeric',
            'data.*.max_supply' => 'nullable|numeric', 
            'data.*.quote.USD.percent_change_1h' => 'required|numeric',
            'data.*.quote.USD.percent_change_24h' => 'required|numeric',
            'data.*.quote.USD.percent_change_7d' => 'required|numeric',
            'data.*.quote.USD.last_updated' => 'required|date',
        ]);

        return $validator;
    }
    protected function processData($data)
    {
        $cryptos = $data['data'];
        foreach ($cryptos as $crypto) {
            $coinInfo = $this->updateOrCreateCoinInfo($crypto);
            $this->createCoinHistory($crypto, $coinInfo);
        }
    }

    protected function updateOrCreateCoinInfo($crypto)
    {
        return CoinInfo::updateOrCreate(
            ['name' => $crypto['name']],
            [
                'symbol' => $crypto['symbol'],
                'rank' => $crypto['cmc_rank'],
                'price_usd' => $crypto['quote']['USD']['price'],
                'market_cap_usd' => $crypto['quote']['USD']['market_cap'],
                'volume_usd_24h' => $crypto['quote']['USD']['volume_24h'],
                'circulating_supply' => $crypto['circulating_supply'],
                'total_supply' => $crypto['total_supply'],
                'max_supply' => $crypto['max_supply'],
                'percent_change_1h' => round($crypto['quote']['USD']['percent_change_1h'], 2),
                'percent_change_24h' => round($crypto['quote']['USD']['percent_change_24h'], 2),
                'percent_change_7d' => round($crypto['quote']['USD']['percent_change_7d'], 2),
                'last_updated' => (new \DateTime($crypto['quote']['USD']['last_updated']))->format('Y-m-d H:i:s')
            ]
        );

    }

    protected function createCoinHistory($crypto, $coinInfo)
    {
        return CoinHistory::create([
            'Fk_coininfo' => $coinInfo->Pk_ID,
            'price_usd' => $crypto['quote']['USD']['price'],
            'volume_usd_24h' => $crypto['quote']['USD']['volume_24h'],
            'timestamp' => (new \DateTime($crypto['quote']['USD']['last_updated']))->format('Y-m-d H:i:s')
        ]);
    }
    protected function handleException($e)
    {
        if ($e instanceof \GuzzleHttp\Exception\ClientException) {
            $response = json_decode($e->getResponse()->getBody(), true);
            if (isset($response['status']['error_code'])) {
                switch ($response['status']['error_code']) {
                    case 1001:
                    case 1002:
                        $this->error('API Key Issue: ' . $response['status']['error_message']);
                        break;
                    case 1003:
                    case 1004:
                        $this->error('Payment Issue: ' . $response['status']['error_message']);
                        break;
                    case 1005:
                    case 1006:
                    case 1007:
                        $this->error('Authorization Issue: ' . $response['status']['error_message']);
                        break;
                    case 1008:
                    case 1009:
                    case 1010:
                    case 1011:
                        $this->error('Rate Limit Issue: ' . $response['status']['error_message']);
                        break;
                    default:
                        $this->error('Client Error (400-level): ' . $e->getMessage());
                }
            }
        } elseif ($e instanceof \GuzzleHttp\Exception\ServerException) {
            $this->error('Server Error (500-level): ' . $e->getMessage());
        } elseif ($e instanceof \GuzzleHttp\Exception\ConnectException) {
            $this->error('Connection Error: ' . $e->getMessage());
        } elseif ($e instanceof \GuzzleHttp\Exception\GuzzleException) {
            $this->error('General Guzzle Error: ' . $e->getMessage());
        } elseif ($e instanceof ValidationException) {
            $this->error('Validation Error: ' . json_encode($e->errors()));
        } else {
            $this->error('An error occurred: ' . $e->getMessage());
        }
    }

}
