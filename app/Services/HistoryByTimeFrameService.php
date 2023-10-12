<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HistoryByTimeFrameService
{
    public function filterHistoryByTimeframe(HasMany $histories, string $timeframe): HasMany 
    {
        switch ($timeframe) {
            case '1D':
                $histories->where('created_at', '>=', Carbon::now()->subDay());
                break;
            case '7D':
                $histories->where('created_at', '>=', Carbon::now()->subWeek());
                break;
            case '1M':
                $histories->where('created_at', '>=', Carbon::now()->subMonth());
                break;
            case '1Y':
                $histories->where('created_at', '>=', Carbon::now()->subYear());
                break;
            case 'ALL':
            default:
                break;
        }

        return $histories;
    }
}