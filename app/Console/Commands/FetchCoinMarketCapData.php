<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Interfaces\CryptoRepositoryInterface;
use App\Services\Validators\APIDAtaValidation;
use Exception;

class FetchCoinMarketCapData extends Command
{
    protected $signature = 'coinmarketcap:fetch';
    protected $description = 'Fetch data from the CoinMarketCap API';
    protected $client;
    protected $cryptoRepository;
    protected $dataValidator;

    public function __construct(Client $client, CryptoRepositoryInterface $cryptoRepository, APIDAtaValidation $dataValidator)
    {
        parent::__construct();
        $this->client = $client;
        $this->cryptoRepository = $cryptoRepository;
        $this->dataValidator = $dataValidator;
    }

    public function handle()
    {
                $data = $this->fetchDataFromAPI();

                $validator = $this->dataValidator->validateData($data);

                if ($validator->fails()) {
                    throw new ValidationException($validator);
                }
    
                $this->processData($data);
    
                $this->info('Data fetched successfully.');
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
                'X-CMC_PRO_API_KEY' => env('CMC_PRO_API_KEY'),
            ],
        ]);

        return json_decode($response->getBody(), true);
    }
    protected function processData($data)
    {
        $cryptos = $data['data'];
        foreach ($cryptos as $crypto) {
            $cryptoInfo = $this->cryptoRepository->createOrUpdateCrypto($crypto);
            $this->cryptoRepository->createCryptoHistory($cryptoInfo);
        }
    }

}
