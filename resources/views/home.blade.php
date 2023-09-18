<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
</head>
<body>

<div class="container mt-4">
    <h1 class="mb-4">Cryptocurrencies</h1>
    
    <!-- Search Form -->
    <form method="GET" action="{{ route('home') }}" class="mb-4">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{ $searchTerm }}">
            <div class="input-group-append">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </div>
    </form>

    <!-- Cryptos Table -->
    <table class="table table-hover">
        <thead class="thead-dark">
            <tr>
                <th>Rank</th>
                <th>Name</th>
                <th>Symbol</th>
                <th>Price (USD)</th>
                <th>1h %</th>
                <th>24h %</th>
                <th>7d %</th>
                <th>Market Cap</th>
                <th>Volume(24h)</th>
                <th>Circulating Supply</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cryptos as $crypto)
            <tr>
                <td>{{ $crypto->rank }}</td>
                <td><a href="{{ route('crypto', $crypto->Pk_ID) }}">{{ $crypto->name }}</a></td>
                <td>{{ $crypto->symbol }}</td>
                <td>
                    @if($crypto->price_usd < 0.1)
                        ${{ number_format($crypto->price_usd, 8) }}
                    @elseif($crypto->price_usd < 1)
                        ${{ number_format($crypto->price_usd, 4) }}
                    @else
                        ${{ number_format($crypto->price_usd, 2) }}
                    @endif
                </td>
                <td>
                    @if($crypto->percent_change_1h < 0)
                        <span class="text-danger">{{ ltrim($crypto->percent_change_1h, '-') }}%</span>
                    @else
                        <span class="text-success">{{ $crypto->percent_change_1h }}%</span>
                    @endif
                </td>
                <td>
                    @if($crypto->percent_change_24h < 0)
                        <span class="text-danger">{{ ltrim($crypto->percent_change_24h, '-') }}%</span>
                    @else
                        <span class="text-success">{{ $crypto->percent_change_24h }}%</span>
                    @endif
                </td>
                <td>
                    @if($crypto->percent_change_7d < 0)
                        <span class="text-danger">{{ ltrim($crypto->percent_change_7d, '-') }}%</span>
                    @else
                        <span class="text-success">{{ $crypto->percent_change_7d }}%</span>
                    @endif
                </td>
                <td>${{ number_format($crypto->market_cap_usd) }}</td>
                <td>${{ number_format($crypto->volume_usd_24h) }}</td>
                <td>
                    {{ number_format($crypto->circulating_supply) }}
                    @if ($crypto->percentage != -1)
                        <div class="progress" role="progressbar" aria-label="Basic example" aria-valuemin="0" aria-valuemax="100">
                            <div class="progress-bar bg-secondary" style="width: {{ $crypto->percentage }}%"></div>
                        </div>
                    @endif
                </td>
                <td>
                            <!-- Add other column data as needed -->
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $cryptos->links() }}
    </div>
</div>

</body>
</html>