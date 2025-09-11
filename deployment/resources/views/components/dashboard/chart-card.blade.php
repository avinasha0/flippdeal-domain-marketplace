@props(['title', 'chartId', 'chartType' => 'line', 'data' => [], 'options' => []])

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg border border-gray-200 dark:border-gray-700">
    <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">{{ $title }}</h3>
        <div class="h-64">
            <canvas id="{{ $chartId }}" class="w-full h-full"></canvas>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('{{ $chartId }}').getContext('2d');
    const isDark = document.documentElement.classList.contains('dark');
    
    const defaultOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    color: isDark ? '#D1D5DB' : '#374151'
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    color: isDark ? '#D1D5DB' : '#374151'
                },
                grid: {
                    color: isDark ? '#374151' : '#E5E7EB'
                }
            },
            y: {
                ticks: {
                    color: isDark ? '#D1D5DB' : '#374151'
                },
                grid: {
                    color: isDark ? '#374151' : '#E5E7EB'
                }
            }
        }
    };
    
    const options = { ...defaultOptions, ...@json($options) };
    
    new Chart(ctx, {
        type: '{{ $chartType }}',
        data: @json($data),
        options: options
    });
});
</script>
@endpush
