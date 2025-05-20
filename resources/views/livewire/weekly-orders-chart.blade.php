<div class="p-4 bg-white dark:bg-neutral-800 rounded-xl border border-neutral-200 dark:border-neutral-700">
    <h2 class="text-lg font-semibold mb-4 text-gray-800 dark:text-white">Weekly Orders - {{ now()->format('F Y') }}</h2>
    <canvas id="weeklyOrdersChart" width="400" height="200"></canvas>

    <script>
        document.addEventListener('livewire:load', function () {
            const ctx = document.getElementById('weeklyOrdersChart').getContext('2d');

            const chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json(collect($weeklyData)->pluck('label')),
                    datasets: [{
                        label: 'Orders',
                        data: @json(collect($weeklyData)->pluck('count')),
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1,
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            stepSize: 1
                        }
                    }
                }
            });
        });
    </script>
</div>
