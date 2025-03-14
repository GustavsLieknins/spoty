<x-app-layout>
    <div class="flex flex-col items-center justify-center py-12 bg-gray-100 dark:bg-gray-900 transition-colors duration-300">
        <h1 class="text-6xl md:text-7xl font-extrabold text-gray-800 dark:text-white mb-6">Top Artists</h1>
        <div class="flex items-center space-x-4 mt-4">
            <div class="text-sm font-light text-gray-500 dark:text-gray-300">
                Range
            </div>
            <form action="{{ route('top.artists') }}" method="GET" onchange="this.submit()">
                @csrf
                <select name="range" onchange="this.form.submit()" class="bg-white dark:bg-gray-700 rounded-lg shadow-lg px-4 py-2 text-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="short_term" {{ old('range', $timeRange) === 'short_term' ? 'selected' : '' }}>short_term</option>
                    <option value="medium_term" {{ old('range', $timeRange) === 'medium_term' ? 'selected' : '' }}>medium_term</option>
                    <option value="long_term" {{ old('range', $timeRange) === 'long_term' ? 'selected' : '' }}>long_term</option>
                </select>
            </form>
        </div>
        <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-8 w-full px-4 md:px-0">
            @foreach ($topArtists as $artist)
            <a href="{{ $artist['external_urls']['spotify'] }}" target="_blank" class="transform hover:scale-105 transition-transform duration-300">
                <li class="bg-white dark:bg-gray-800 rounded-xl shadow-xl p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <strong class="text-xl md:text-2xl font-semibold text-gray-800 dark:text-white">{{ $artist['name'] }}</strong>
                        <span class="text-xs font-light text-gray-500 dark:text-gray-400">{{ $artist['genres'] ? implode(', ', $artist['genres']) : 'No genre available' }}</span>
                    </div>
                    <img src="{{ $artist['images'][0]['url'] }}" alt="{{ $artist['name'] }} cover" class="w-full h-auto rounded-lg mt-4 shadow-lg">
                </li>
            </a>
            @endforeach
        </ul>
    </div>
</x-app-layout>

