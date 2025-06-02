<div class="container" wire:ignore>
    <div class="card">
        <div class="flex flex-wrap justify-center gap-4 bg-gray-100 p-4 rounded-xl shadow-sm">
            <a href="{{ url('warehouse') }}" class="text-blue-700 font-medium hover:underline hover:text-blue-900">
                Warehouse Items
            </a>
            <a href="{{ url('import') }}" class="text-blue-700 font-medium hover:underline hover:text-blue-900">
                Warehouse Items StockQty
            </a>
            <a href="{{ url('wareHouseExport') }}"
                class="text-blue-700 font-medium hover:underline hover:text-blue-900">
                Export StockQty
            </a>
            <a href="{{ url('stockExport') }}" class="text-blue-700 font-medium hover:underline hover:text-blue-900">
                Export Value Differences
            </a>
        </div>


        <div class="card-body">
            <canvas id="myChart"></canvas>
            <div class="d-flex flex-wrap gap-5 mt-4">
                <canvas id="myChart2" style="flex: 1; max-width: 48%;"></canvas>
                <canvas id="myChart3" style="flex: 1; max-width: 48%;"></canvas>
            </div>

            <div class="container mt-4">
                <h2 class="mb-3">Stock Value Report (Last 13 Months)</h2>
                <div class="table-responsive">
                    <table class="table-default">
                        <thead class="table-highlighted">
                            <tr>
                                <th>Month</th>
                                <th>Total EUR</th>
                                <th>Previous Month EUR</th>
                                <th>Difference</th>
                                <th>Percentage Change</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($totalStockValues as $data)
                            <tr>
                                <td>{{ $data['month'] }}</td>
                                <td>{{ number_format($data['total_EUR'], 2) }} €</td>
                                <td>
                                    {{ $data['previous_total_EUR'] ? number_format($data['previous_total_EUR'], 2) . '
                                    €' : '-' }}
                                </td>
                                <td class="{{ $data['difference'] >= 0 ? 'text-danger' : 'text-success' }}">
                                    {{ number_format($data['difference'], 2) }} €
                                </td>
                                <td class="{{ $data['percentage_difference'] >= 0 ? 'text-danger' : 'text-success' }}">
                                    {{ $data['percentage_difference'] !== null
                                    ? number_format($data['percentage_difference'], 2) . ' %'
                                    : '-' }}
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
    const ctx = document.getElementById('myChart').getContext('2d');
  const stocks = $wire.stocks;

  // Get unique months
  const uniqueMonths = [...new Set(stocks.map(item => item.month))]; 

  // Separate total rows from categories
  const totalData = uniqueMonths.map(month => {
    const totalRow = stocks.find(item => item.month === month && item.category === 'Total');
    return totalRow ? totalRow.total_eur : 0;
  });

  // Get all categories except 'Total'
  const categories = [...new Set(stocks.map(item => item.category))].filter(c => c !== 'Total');

  // Prepare the data for each category per month
  const dataPerCategory = categories.map(category => {
    return uniqueMonths.map(month => {
      const dataForMonth = stocks.find(item => item.month === month && item.category === category);
      return dataForMonth ? dataForMonth.total_eur : 0;
    });
  });

  const data = {
    labels: uniqueMonths, // Months as x-axis labels
    datasets: [
      // Add category datasets
      ...categories.map((category, index) => ({
        label: category,
        data: dataPerCategory[index],
        backgroundColor: [
          'rgb(255, 99, 132)',
          'rgb(54, 162, 235)',
          'rgb(255, 205, 86)',
          'rgba(153, 102, 255)',
        ][index % 4], // Cycle through colors
        hoverOffset: 4
      })),
      // Add Total dataset as a line
      {
        label: 'Total EUR',
        data: totalData,
        type: 'line', // Line to distinguish total
        borderColor: 'rgba(0, 0, 0, 1)',
        backgroundColor: 'rgba(0, 0, 0, 0.2)',
        borderWidth: 2,
        pointRadius: 5,
        fill: false
      }
    ]
  };

  new Chart(ctx, {
    type: 'bar',
    data: data,
    options: {
      responsive: true,
      plugins: {
        tooltip: {
          callbacks: {
            title: function(tooltipItem) {
              return 'Month: ' + tooltipItem[0].label;
            }
          }
        }
      },
      scales: {
        y: {
          beginAtZero: true
        },
        x: {
          type: 'category',
          labels: uniqueMonths
        }
      }
    }
  });
</script>
@endscript

@script
<script>
    const ctx2 = document.getElementById('myChart2').getContext('2d');
  const totalStockValues = $wire.totalStockValues || [];

  if (!totalStockValues.length) {
    console.warn("No stock values available, chart not rendered.");
    return; // Prevent rendering an empty chart
  }

  const Mylabels = totalStockValues.map(item => item.month);
  const MYvalues = totalStockValues.map(item => item.difference);

  // Define background colors: Green for positive, Red for negative values
  const backgroundColors = MYvalues.map(value => value >= 0 ?   'rgba(255, 99, 132, 0.5)':'rgba(75, 192, 192, 0.5)' );
  const borderColors = MYvalues.map(value => value >= 0 ?'rgba(255, 99, 132, 1)': 'rgba(75, 192, 192, 1)' );

  new Chart(ctx2, {
    type: 'bar',
    data: {
      labels: Mylabels,
      datasets: [
        {
          label: 'Difference in EUR',
          data: MYvalues,
          backgroundColor: backgroundColors,
          borderColor: borderColors,
          borderWidth: 1
        },
      ]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true
        }
      }
    }
  });
</script>
@endscript

@script
<script>
    const ctx3 = document.getElementById('myChart3').getContext('2d');
  const stockPercentage = $wire.totalStockValues || [];

  if (!stockPercentage.length) {
    console.warn("No stock values available, chart not rendered.");
    return; // Prevent rendering an empty chart
  }

  const Mylabels = stockPercentage.map(item => item.month);
  const MYvalues2 = stockPercentage.map(item => item.percentage_difference);

  // Define colors based on positive or negative values
  const backgroundColors2 = MYvalues2.map(value => value >= 0 ? 'rgba(153, 102, 255, 0.2)' : 'rgba(255, 205, 86, 0.2)' );
  const borderColors2 = MYvalues2.map(value => value >= 0 ?  'rgba(255, 99, 132, 1)' : 'rgba(75, 192, 192, 1)');

  new Chart(ctx3, {
    type: 'bar',
    data: {
      labels: Mylabels,
      datasets: [
        {
          label: 'Difference in Percentage',
          data: MYvalues2,
          backgroundColor: backgroundColors2,
          borderColor: borderColors2,
          borderWidth: 1
        }
      ]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return value + '%'; // Add percentage symbol to Y-axis
            }
          }
        }
      },
      plugins: {
        tooltip: {
          callbacks: {
            label: function(tooltipItem) {
              return tooltipItem.raw + '%'; // Show percentage in tooltip
            }
          }
        }
      }
    }
  });
</script>
@endscript