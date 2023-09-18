<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="{{ asset('js/chart.js') }}"></script>
</head>
<body>
<input type="hidden" id="cryptoId" value="{{ $crypto->Pk_ID }}">
<div class="container-fluid">
    <div class="row">
        <div class="col-2">
            <div>{{$crypto->name}}</div>
            <h1>
                    @if($crypto->price_usd < 0.1)
                        ${{ number_format($crypto->price_usd, 8) }}
                    @elseif($crypto->price_usd < 1)
                        ${{ number_format($crypto->price_usd, 4) }}
                    @else
                        ${{ number_format($crypto->price_usd, 2) }}
                    @endif
            </h1>
            <div>Market cap: ${{number_format($crypto->market_cap_usd)}}</div>
            <div>Volume(24h): ${{number_format($crypto->volume_usd_24h)}}</div>
            <div>Circulating supply: {{number_format($crypto->circulating_supply)}} {{$crypto->symbol}}</div>
            <div>Total supply: {{number_format($crypto->total_supply)}} {{$crypto->symbol}}</div>
            <div>Max supply: {{number_format($crypto->max_supply)}} {{$crypto->symbol}}</div>

        </div>
        <div class="col-10">
        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
            <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked>
            <label class="btn btn-outline-primary" for="btnradio1">1D</label>

            <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
            <label class="btn btn-outline-primary" for="btnradio2">7D</label>

            <input type="radio" class="btn-check" name="btnradio" id="btnradio3" autocomplete="off">
            <label class="btn btn-outline-primary" for="btnradio3">1M</label>

            <input type="radio" class="btn-check" name="btnradio" id="btnradio4" autocomplete="off">
            <label class="btn btn-outline-primary" for="btnradio4">1Y</label>

            <input type="radio" class="btn-check" name="btnradio" id="btnradio5" autocomplete="off">
            <label class="btn btn-outline-primary" for="btnradio5">ALL</label>
        </div>
        <div id="chart"></div>
        <script>
            window.chartData = @json($chartData);
            window.cryptoName = '{{ $crypto->name }}';
        </script>
        </div>
    </div>
</div>
</body>
</html>