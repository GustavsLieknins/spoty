<x-app-layout>
    <div class="flex flex-col items-center justify-center py-12 bg-gray-100 dark:bg-gray-800">
        <h1 class="text-6xl font-bold text-gray-800 dark:text-white">Top Songs</h1>
        <div class="flex items-center space-x-4 mt-4">
            <div class="text-sm font-light text-gray-500 dark:text-gray-300">
                Range
            </div>
            <form action="{{ route('top.songs') }}" method="GET" onchange="this.submit()">
                @csrf
                <select name="range" onchange="this.form.submit()" class="bg-white dark:bg-gray-700 rounded-lg shadow-md px-4 py-2 text-gray-700 dark:text-white">
                    <option value="short_term" {{ old('range', $timeRange) === 'short_term' ? 'selected' : '' }}>short_term</option>
                    <option value="medium_term" {{ old('range', $timeRange) === 'medium_term' ? 'selected' : '' }}>medium_term</option>
                    <option value="long_term" {{ old('range', $timeRange) === 'long_term' ? 'selected' : '' }}>long_term</option>
                </select>
            </form>
        </div>
        <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
            @foreach ($topSongs['items'] as $song)
            <a href="{{ $song['external_urls']['spotify'] }}" target="_blank">
                <li class="bg-white dark:bg-gray-700 rounded-lg shadow-md p-4">
                    <div class="flex items-center justify-between">
                        <strong class="text-2xl font-bold text-gray-800 dark:text-white">{{ $song['name'] }}</strong>
                        <span class="text-xs font-light text-gray-500 dark:text-gray-300">{{ implode(', ', array_column($song['artists'], 'name')) }}</span>
                    </div>
                    <img src="{{ $song['album']['images'][0]['url'] }}" alt="{{ $song['name'] }} cover" class="w-full h-auto rounded-lg mt-4" width="100" height="100">
                </li>
            </a>
            @endforeach
        </ul>
    </div>
</x-app-layout>

