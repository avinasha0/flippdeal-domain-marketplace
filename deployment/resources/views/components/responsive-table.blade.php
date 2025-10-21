@props(['headers' => [], 'rows' => [], 'mobileView' => true])

<div class="overflow-hidden">
    <!-- Desktop Table -->
    <div class="hidden md:block overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    @foreach($headers as $header)
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($rows as $row)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150">
                        @foreach($row as $cell)
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                {{ $cell }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Mobile Cards -->
    @if($mobileView)
        <div class="md:hidden space-y-4">
            @foreach($rows as $index => $row)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    <div class="space-y-2">
                        @foreach($row as $cellIndex => $cell)
                            <div class="flex justify-between items-start">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ $headers[$cellIndex] ?? 'Field' }}
                                </span>
                                <span class="text-sm text-gray-900 dark:text-gray-100 text-right ml-4">
                                    {{ $cell }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
