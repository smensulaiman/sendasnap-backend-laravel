@props([
    'id' => 'donutChart',
    'labels' => [],
    'data' => [],
    'colors' => [],
    'legend' => true,
    'cutout' => '65%',
    'height' => '100%',
    'minHeight' => '280px',
])

<div style="width: 100%; height: {{ $height }}; min-height: {{ $minHeight }}; display: flex; align-items: center; justify-content: center;">
    <canvas id="{{ $id }}" style="width: 100%; height: 100%; display: block;"></canvas>
</div>

<script>
    (function() {
        const el = document.getElementById(@json($id));
        if (!el) return;

        const chartData = {
            labels: @json(array_values($labels)),
            datasets: [{
                label: 'Tasks',
                data: @json(array_values($data)),
                backgroundColor: @json(array_values($colors)),
                borderWidth: 0,
                borderColor: 'transparent',
                hoverOffset: 8,
                radius: '95%',
            }]
        };

        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            layout: { padding: 0 },
            elements: { arc: { borderRadius: 8 } },
            plugins: {
                legend: {
                    display: @json($legend),
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        color: getComputedStyle(document.documentElement).getPropertyValue('--muted-foreground') ? `hsl(${getComputedStyle(document.documentElement).getPropertyValue('--muted-foreground')})` : '#6b7280',
                        font: { size: 12, family: "'Montserrat', sans-serif", weight: '600' }
                    }
                },
                tooltip: {
                    backgroundColor: 'hsl(var(--text))',
                    titleFont: { family: "'Montserrat', sans-serif", weight: 'bold' },
                    bodyFont: { family: "'Montserrat', sans-serif" },
                    padding: 12,
                    displayColors: true,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const pct = total > 0 ? Math.round((value / total) * 100) : 0;
                            return `${label}: ${value} (${pct}%)`;
                        }
                    }
                }
            },
            cutout: @json($cutout)
        };

        function renderChart() {
            const instance = new Chart(el, { type: 'doughnut', data: chartData, options: chartOptions });
            window[@json($id) + 'Instance'] = instance;
            if (@json($id) === 'scheduleChart') {
                window.scheduleChart = instance;
            }
            window.addEventListener('resize', () => instance.resize());
        }

        if (!window.Chart) {
            const s = document.createElement('script');
            s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js';
            s.onload = renderChart;
            document.head.appendChild(s);
        } else {
            renderChart();
        }
    })();
    </script>


