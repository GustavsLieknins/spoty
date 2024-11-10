<x-app-layout>
    <div class="flex flex-col items-center justify-center py-12 bg-gray-100 dark:bg-gray-800">
        <h1 class="text-6xl font-bold text-gray-800 dark:text-white">Your Top Genres</h1>
        <ul class="space-y-6 mt-8 w-full max-w-3xl">
            @php
                $maxPercentage = $genresCount[0]['percentage'] ?? 0;
            @endphp
            @foreach ($genresCount as $index => $genre)
                @php
                    $normalizedPercentage = $index == 0 ? 100 : round(($genre['percentage'] / $maxPercentage) * 100);
                @endphp
                <li class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-6">
                    <div class="flex items-center justify-between">
                        <div class="text-2xl font-bold text-gray-800 dark:text-white">{{ ucfirst($genre['genre']) }}</div>
                        <!-- <div class="text-lg font-semibold text-gray-600 dark:text-gray-400">{{ $genre['percentage'] }}% ({{ $normalizedPercentage }}%)</div> -->
                        <div class="text-lg font-semibold text-gray-600 dark:text-gray-400">{{ $genre['percentage'] }}%</div>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-3 mt-4">
                        <div class="bg-blue-600 h-3 rounded-full" style="width: {{ $normalizedPercentage }}%;"></div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</x-app-layout>
