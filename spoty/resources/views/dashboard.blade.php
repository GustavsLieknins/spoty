<x-app-layout>
    <div class="flex flex-col items-center justify-center py-12 bg-gradient-to-br from-black via-gray-900 to-gray-950">
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

        <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mt-12 w-full max-w-6xl px-4">
            @foreach ($topSongs['items'] as $song)
            <li class="bg-white dark:bg-gray-700 rounded-xl shadow-lg p-6 transition hover:scale-105 hover:shadow-2xl">
                <a href="{{ $song['external_urls']['spotify'] }}" target="_blank" class="block">
                    <div class="relative w-[300px] h-[300px] overflow-hidden rounded-lg bg-gray-200 mx-auto">
                        <img 
                            src="{{ $song['album']['images'][0]['url'] }}" 
                            alt="{{ $song['name'] }} cover" 
                            class="absolute inset-0 w-full h-full object-cover"
                        >
                    </div>
                    <div class="flex flex-col mt-4 text-center">
                        <span class="text-lg font-bold text-gray-800 dark:text-white truncate">{{ $song['name'] }}</span>
                        <span class="text-sm text-gray-500 dark:text-gray-300 mt-1 truncate">{{ implode(', ', array_column($song['artists'], 'name')) }}</span>
                    </div>
                </a>
            </li>
            @endforeach
        </ul>
    </div>
</x-app-layout>
