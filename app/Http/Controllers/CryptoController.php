<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CoinInfo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;

class CryptoController extends Controller
{
    public function index(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'search' => 'sometimes|required|string|max:255',
            ]);
            
            $searchTerm = $validatedData['search'] ?? '';

            $cryptos = CoinInfo::when($searchTerm, function ($query, $searchTerm) {
                return $query->where('name', 'LIKE', "%{$searchTerm}%");
            })->orderBy('rank', 'asc') 
            ->paginate(100);

            $cryptos->transform(function ($crypto) {
                $crypto->percentage = ($crypto['max_supply'] != 0) 
                    ? $crypto['circulating_supply'] * 100 / $crypto['max_supply'] 
                    : -1;
                return $crypto;
            });
            
            return view('home', ['cryptos' => $cryptos, 'searchTerm' => $searchTerm]);

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        }
    }

    public function show(Request $request, $id, $timeframe = '1D')
    {
        try {
            $validator = Validator::make(['timeframe' => $timeframe], [
                'timeframe' => 'in:1D,7D,1M,1Y,ALL',
            ]);
            
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
            $crypto = CoinInfo::findOrFail($id);
            $query = $crypto->coinHistories();
            
            $this->filterHistoryByTimeframe($query, $timeframe);

            $history = $query->get();

            $chartData = $history->map(function ($record) {
                return [
                    'x' => $record->timestamp,
                    'y' => (float) $record->price_usd
                ];
            });

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'cryptoName' => $crypto->name,
                    'chartData' => $chartData
                ]);
            }
            

            return view('crypto', compact('crypto', 'chartData', 'history'));

        } catch (ModelNotFoundException $e) {
            return $this->handleException($request, $e, 'Data not found.', 404);
        } 
        catch (ValidationException $e) {
            return $this->handleException($request, $e, 'Validation Error.', 400);
        } 
        catch (\Exception $e) {
            return $this->handleException($request, $e, 'Internal Server Error.', 500);
        }

    }
    private function filterHistoryByTimeframe($histories, $timeframe)
    {
        switch ($timeframe) {
            case '1D':
                $histories->where('timestamp', '>=', Carbon::now()->subDay());
                break;
            case '7D':
                $histories->where('timestamp', '>=', Carbon::now()->subWeek());
                break;
            case '1M':
                $histories->where('timestamp', '>=', Carbon::now()->subMonth());
                break;
            case '1Y':
                $histories->where('timestamp', '>=', Carbon::now()->subYear());
                break;
            case 'ALL':
            default:
                break;
        }
    }
    private function handleException($request, \Exception $e, $defaultMessage, $errorCode)
    {
        $message = $e instanceof ValidationException ? $e->errors() : $defaultMessage;

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['error' => $message], $errorCode);
        }

        return response()->view("errors.{$errorCode}", [], $errorCode);
    }
}
