<x-app-layout>
    <div class="min-h-screen flex flex-col items-center justify-start pt-4 bg-gray-100 dark:bg-gray-800">
        <div class="container mx-auto px-4 py-8">
            <h1 class="text-4xl font-bold text-gray-800 dark:text-white">Create a new playlist</h1>
            <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">It's going to place your top songs of the last 30 days!</p>
            <form method="POST" action="{{ route('create.create') }}" class="mt-8 space-y-6">
                @csrf
                <div class="relative">
                    <label for="playlist_name" class="block text-sm font-medium text-white">Playlist Name:</label>
                    <input type="text" id="playlist_name" name="playlistName" class="text-white mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" value="{{ old('playlist_name') }}" required autofocus />
                </div>
                <div class="relative">
                    <label for="playlist_description" class="block text-sm font-medium text-white">Playlist Description:</label>
                    <input type="text" id="playlist_description" name="playlistDescription" class="text-white mt-1 block w-full px-3 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" value="{{ old('playlist_description') }}" />
                </div>
                <div class="flex items-center justify-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-700 text-white font-semibold rounded-md shadow-sm">
                        Create Playlist
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

