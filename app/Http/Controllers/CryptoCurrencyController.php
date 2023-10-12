<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Http\Requests\ShowRequest;
use Illuminate\Http\Request;
use App\Interfaces\CryptoRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use App\Services\HistoryByTimeFrameService;
use App\Services\TransformCryptoAddPercentage;
use Exception;

class CryptoCurrencyController extends Controller
{
    protected $historyService;
    protected $cryptoRepository;
    protected $transformService;

    public function __construct(HistoryByTimeFrameService $historyService, CryptoRepositoryInterface $cryptoRepository,
    TransformCryptoAddPercentage $transformService)
    {
        $this->historyService = $historyService;
        $this->cryptoRepository = $cryptoRepository;
        $this->transformService = $transformService;
    }

    public function index(Request $request)
    {
        try {
            $cryptos = $this->cryptoRepository->getCryptosOrderedBy('rank', 100);
            $cryptos = $this->transformService->addSupplyPercentageToCryptos($cryptos);

            return view('home', ['cryptos' => $cryptos]);

        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors());
        }
    }
    public function search(SearchRequest $request)
    {
        $searchTerm = $request['search'];

        $cryptos = $this->cryptoRepository->searchCryptosByTerm('name', $searchTerm, 'rank', 100);
        $cryptos = $this->transformService->addSupplyPercentageToCryptos($cryptos);

        return view('home', ['cryptos' => $cryptos, 'searchTerm' => $searchTerm]);
    }

    public function show(ShowRequest $request, int $id, string $timeframe = '1D')
    {
        try {
            $crypto = $this->cryptoRepository->findCryptoById($id);

            $histories = $crypto->cryptoHistory();
            $this->historyService->filterHistoryByTimeframe($histories, $timeframe);

            $history = $histories->get();

            $chartData = $history->map(function ($record) {
                return [
                    'x' => $record->created_at,
                    'y' => (float) $record->price
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
        catch (Exception $e) {
            return $this->handleException($request, $e, 'Internal Server Error.', 500);
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
