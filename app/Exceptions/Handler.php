<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Throwable;
use \GuzzleHttp\Exception\ClientException;
use \GuzzleHttp\Exception\ServerException;
use \GuzzleHttp\Exception\ConnectException;
use \GuzzleHttp\Exception\GuzzleException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof QueryException) {
            Log::error("Database query error: " . $exception->getMessage());
            return response()->view('errors.query', [], 500);
        }
        elseif ($this->isApiException($exception)) {
            return $this->handleApiException($request, $exception);
        }
        
        return parent::render($request, $exception);
    }
    protected function handleApiException($request, Throwable $e)
    {
        $errorMessage = 'An error occurred';
    
        if ($e instanceof ClientException) {
            $response = json_decode($e->getResponse()->getBody(), true);
            if (isset($response['status']['error_code'])) {
                switch ($response['status']['error_code']) {
                    case 1001:
                    case 1002:
                        $errorMessage = 'API Key Issue: ' . $response['status']['error_message'];
                        break;
                    case 1003:
                    case 1004:
                        $errorMessage = 'Payment Issue: ' . $response['status']['error_message'];
                        break;
                    case 1005:
                    case 1006:
                    case 1007:
                        $errorMessage = 'Authorization Issue: ' . $response['status']['error_message'];
                        break;
                    case 1008:
                    case 1009:
                    case 1010:
                    case 1011:
                        $errorMessage = 'Rate Limit Issue: ' . $response['status']['error_message'];
                        break;
                    default:
                        $errorMessage = 'Client Error (400-level): ' . $e->getMessage();
                }
            }
        } elseif ($e instanceof ServerException) {
            $errorMessage = 'Server Error (500-level): ' . $e->getMessage();
        } elseif ($e instanceof ConnectException) {
            $errorMessage = 'Connection Error: ' . $e->getMessage();
        } elseif ($e instanceof GuzzleException) {
            $errorMessage = 'General Guzzle Error: ' . $e->getMessage();
        }
    
        Log::error($errorMessage);
        
        if ($request->expectsJson()) {
            return response()->json(['error' => $errorMessage], 500);
        }
        
        return response()->view('errors.api', ['error' => $errorMessage], 500);
    }
    protected function isApiException(Throwable $e)
    {
        return $e instanceof ClientException 
            || $e instanceof ServerException 
            || $e instanceof ConnectException 
            || $e instanceof GuzzleException;
    }
}
