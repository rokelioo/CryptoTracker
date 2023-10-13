<?php

namespace App\Services\Validators;

use Illuminate\Support\Facades\Validator;

class APIDAtaValidation{
    public function validateData(array $data)
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
            'data.*.quote.USD.percent_change_1h' => 'nullable|numeric', 
            'data.*.quote.USD.percent_change_24h' => 'nullable|numeric',  
            'data.*.quote.USD.percent_change_7d' => 'nullable|numeric', 
            'data.*.quote.USD.last_updated' => 'required|date',
        ]);

        return $validator;
    }
}