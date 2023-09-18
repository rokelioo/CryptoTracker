var chart; // Make it globally accessible.
document.addEventListener("DOMContentLoaded", function(event) {
    var chartData = window.chartData || [];

    document.querySelectorAll("input[name='btnradio']").forEach(function(radio) {
        radio.addEventListener('change', function() {
    const selectedTimeFrame = this.nextElementSibling.textContent.trim();
    const cryptoId = document.getElementById("cryptoId").value;
    fetchDataAndUpdateChart(cryptoId, selectedTimeFrame);
});
    });
    

    var options = {
        series: [{
            name: window.cryptoName,
            data: chartData
        }],
        chart: {
        type: 'area',
        stacked: false,
        height: 350,
        zoom: {
            type: 'x',
            enabled: true,
            autoScaleYaxis: true
        },
        toolbar: {
            autoSelected: 'zoom'
        }
        },
        dataLabels: {
        enabled: false
        },
        markers: {
        size: 0,
        },
        title: {
        text: 'Stock Price Movement',
        align: 'left'
        },
        fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            inverseColors: false,
            opacityFrom: 0.5,
            opacityTo: 0,
            stops: [0, 90, 100]
        },
        },
        yaxis: {
        labels: {
            formatter: function (val) {
                if (val >= 1000) {
                    return (val / 1000).toFixed(2) + 'K';
                } else if (val < 0.0001 && val > 0) { 
                    return val.toFixed(8); 
                } else if (val <= 0.1 && val > 0) {
                    return val.toFixed(5); 
                } else if (val < 100) { 
                    return val.toFixed(4); 
                } else {
                    return val.toFixed(2);
                }
            },
        },
        title: {
            text: 'Price'
        },
        },
        xaxis: {
        type: 'datetime',
        },
        tooltip: {
        shared: false,
        y: {
            formatter: function (val) {
                if (val >= 1000) {
                    return (val / 1000).toFixed(2) + 'K';
                } else if (val < 0.0001 && val > 0) { 
                    return val.toFixed(8); 
                } else if (val <= 0.1 && val > 0) {
                    return val.toFixed(5); 
                } else if (val < 100) { 
                    return val.toFixed(4); 
                } else {
                    return val.toFixed(2);
                }
            }
        }
        }
        };

    chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();
});
function fetchDataAndUpdateChart(cryptoId, timeframe) {
    fetch(`/${cryptoId}/${timeframe}`,{
        headers: {
            "Accept": "application/json"
        }
    })
.then(response => {
  if (!response.ok) {
    throw new Error('Network response was not ok');
  }
  return response.json(); // directly return parsed JSON
})
.then(data => {
  chart.updateSeries([{
    name: data.cryptoName,
    data: data.chartData
  }]);
})
.catch(error => {
  console.error('Error fetching data:', error);
}); 
}
