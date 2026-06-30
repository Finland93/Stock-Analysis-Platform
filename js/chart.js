// Requests go through app/api-proxy.php so the API key stays server-side.
var modal = document.getElementById("chart-modal");
var symbol, StockName, apiKey;
var btn = document.querySelectorAll(".symbol-button");
var chart;
var fibData = [];
var retracementLevels = [0, 0.236, 0.382, 0.5, 0.618, 0.786, 1];
var closingPrices = [];

for (var i = 0; i < btn.length; i++) {
  btn[i].addEventListener("click", async function() {
    symbol = this.dataset.symbol;
    StockName = this.dataset.name;
    var barData = [];
    fibData = Array.from({length: retracementLevels.length}, () => []);
    closingPrices = [];

    var ctx = document.getElementById('chart').getContext('2d');
    function setCanvasSize() {
      if (window.innerWidth < 500) {
        ctx.canvas.width = window.innerWidth - 20;
        ctx.canvas.height = 500;
      } else {
        ctx.canvas.width = 800;
        ctx.canvas.height = 400;
      }
    }

    setCanvasSize();
    window.addEventListener('resize', setCanvasSize);
	

    await fetchData(barData);
    createChart(ctx, barData, fibData, closingPrices);
    modal.style.display = "block";
	
  });
}

var span = document.getElementsByClassName("close")[0];
span.onclick = function() {
  modal.style.display = "none";
  chart.destroy();
};

window.onclick = function(event) {
  if (event.target == modal) {
    modal.style.display = "none";
    chart.destroy();
  }
};

async function fetchData(barData) {
  var url = `app/api-proxy.php?type=chart&symbol=${encodeURIComponent(symbol)}`;

  try {
    const response = await fetch(url);
    const data = await response.json();
    var timeSeries = data["Time Series (Daily)"];
    if (!timeSeries) { return; }
    // Alpha Vantage returns newest-first; charts need oldest-first order
    var __dates = Object.keys(timeSeries).sort(function(a, b) { return new Date(a) - new Date(b); });

var high = -Infinity;
var low = Infinity;
for (var date of __dates) {
  var prices = timeSeries[date];
  var bar = {
    x: new Date(date).valueOf(),
    o: parseFloat(prices["1. open"]),
    h: parseFloat(prices["2. high"]),
    l: parseFloat(prices["3. low"]),
    c: parseFloat(prices["4. close"])
  };
  barData.push(bar);

  high = Math.max(high, bar.h);
  low = Math.min(low, bar.l);
}

for (var i = 0; i < retracementLevels.length; i++) {
  var level = retracementLevels[i];
  var retracement = low + (high - low) * level;
  var fibDataForLevel = [];
  for (var date of __dates) {
    var fibPoint = {
      x: new Date(date).valueOf(),
      y: retracement
    };
    fibDataForLevel.push(fibPoint);
  }
  fibData[i] = fibDataForLevel;
}
  } catch (error) {
    console.error(error);
  }
}




function createChart(ctx, barData, fibData, closingPrices) {
  chart = new Chart(ctx, {
    type: 'candlestick',
    data: {
      datasets: [
        {
          label: `${symbol} | ${StockName}`,
          data: barData,
          interactive: true,
          candleStickColor: {
            up: '#6AC288',
            down: '#F12665'
          }
        },
		...fibData.map((data, i) => ({
		type: 'line',
		label: (retracementLevels[i] * 100).toFixed(1) + '%',
		data,
		borderColor: i % 2 === 0 ? '#DEA42C' : '#2E86DE',
		borderWidth: 1,
		fill: false,
		lineTension: 0,
	}))
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: true,
      plugins: {
        legend: {
          display: true,
          labels: { color: 'white' }
        },
        tooltip: {
          mode: 'index',
          intersect: false,
          callbacks: {
            label: function(ctx) {
              var label = ctx.dataset.label || '';
              var y = (ctx.parsed && typeof ctx.parsed.y === 'number') ? ctx.parsed.y : null;
              return y === null ? label : (label + ' ' + y.toFixed(2));
            }
          }
        }
      },
      scales: {
        x: {
          type: 'time',
          time: { unit: 'day' },
          grid: { color: 'rgba(255, 255, 255, 1)' },
          ticks: { color: 'white', autoSkip: true, maxTicksLimit: 10 }
        },
        y: {
          display: true,
          title: { display: true, text: 'Price', color: 'white' },
          grid: { color: 'rgba(255, 255, 255, 1)' },
          ticks: {
            color: 'white',
            callback: function(value) { return Number(value).toFixed(2); }
          }
        }
      },
      layout: {
        padding: { left: 0, right: 0, top: 5, bottom: 5 }
      }
    }
  });
}