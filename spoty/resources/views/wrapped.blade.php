<x-app-layout>
    <div id="wrappedContent" class="min-h-screen bg-gradient-to-br from-black via-gray-900 to-gray-950 text-white px-6 py-16 flex flex-col items-center justify-center space-y-12 relative">

      <style>
        * {
          box-sizing: border-box;
          line-height: 1.6 !important;
          -webkit-font-smoothing: antialiased;
          -moz-osx-font-smoothing: grayscale;
        }
        #wrappedContent {
          /* padding-bottom: 6rem; space for bottom text */
        }
        body, html {
          margin: 0;
          padding: 0;
          background: #0f172a;
        }
        .capture-container {
          background: #0f172a;
        }
        .bg-fixed-for-capture {
          background: #0f172a !important;
        }
      </style>
  
      <!-- Title -->
      <h1 class="text-5xl md:text-7xl font-black text-center drop-shadow-lg">
        Your Gustify Wrapped
      </h1>
  
      <!-- Download Button -->
      <div>
        <button id="downloadBtn" onclick="downloadWrapped()" class="download-ignore px-6 py-2 rounded-full bg-green-500 hover:bg-green-600 text-white text-lg font-semibold shadow-xl transition">
          📥 Download Your Wrapped
        </button>
      </div>
  
      <!-- Time Range -->
      <form method="GET" action="{{ route('wrapped.show') }}">
        <select name="range" onchange="this.form.submit()" class="download-ignore px-5 py-2 rounded-full bg-gray-800 text-white border border-gray-700 shadow-md">
          <option value="short_term" {{ $timeRange === 'short_term' ? 'selected' : '' }}>Last month</option>
          <option value="medium_term" {{ $timeRange === 'medium_term' ? 'selected' : '' }}>Last 6 Months</option>
          <option value="long_term" {{ $timeRange === 'long_term' ? 'selected' : '' }}>All Time</option>
        </select>
      </form>
  
      <!-- Grid Display -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8 w-full max-w-7xl text-white">
  
        <!-- Top Songs -->
        <div class="bg-white/20 rounded-2xl p-6 shadow-lg h-[500px] overflow-y-auto">
          <h2 class="text-2xl font-bold text-purple-300 mb-4">🎵 Top 5 Songs</h2>
          <ul class="space-y-4">
            @foreach ($topSongs['items'] as $index => $song)
              <li class="flex items-center space-x-4">
                <img src="{{ $song['album']['images'][2]['url'] }}" alt="{{ $song['name'] }}" class="w-12 h-12 rounded shadow-md">
                <div class="flex-1">
                  <p class="font-semibold text-white text-base leading-snug">{{ $index + 1 }}. {{ $song['name'] }}</p>
                  <p class="text-sm text-gray-400 leading-tight">{{ implode(', ', array_column($song['artists'], 'name')) }}</p>
                </div>
              </li>
            @endforeach
          </ul>
        </div>
  
        <!-- Top Artists -->
        <div class="bg-white/20 rounded-2xl p-6 shadow-lg h-[500px] overflow-y-auto">
          <h2 class="text-2xl font-bold text-pink-300 mb-4">🧑‍🎤 Top 5 Artists</h2>
          <ul class="grid grid-cols-2 gap-4">
            @foreach ($topArtists as $artist)
              <li class="flex flex-col items-center text-center space-y-2">
                <img src="{{ $artist['images'][0]['url'] ?? '/placeholder.jpg' }}"
                     alt="{{ $artist['name'] }}"
                     class="w-20 h-20 rounded-full object-cover shadow-md ring-2 ring-purple-400/20">
                <p class="text-white font-medium text-sm leading-[1.4rem] tracking-tight">
                  {{ $artist['name'] }}
                </p>
              </li>
            @endforeach
          </ul>
        </div>
  
        <!-- Top Genre -->
        <div class="bg-white/20 rounded-2xl p-6 shadow-lg flex flex-col justify-center items-center h-[500px]">
          <h2 class="text-2xl font-bold text-amber-300 mb-4">🔥 Top Genre</h2>
          <p class="text-4xl font-extrabold text-green-400 animate-pulse">{{ $topGenre }}</p>
        </div>
      </div>
      
      <!-- Hidden container for capture -->
      <div id="captureContainer" class="capture-container absolute top-0 left-0 w-full -z-10 opacity-0"></div>
    </div>
  
    <!-- HTML2Canvas Export -->
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script>
      async function downloadWrapped() {
        const target = document.getElementById('wrappedContent');
        
        // Create a temporary container for capture
        const tempContainer = document.createElement('div');
        tempContainer.className = 'relative w-full min-h-screen bg-fixed-for-capture';
        tempContainer.style.background = '#0f172a'; // Solid background for capture
        
        // Clone the content
        const contentClone = target.cloneNode(true);
        
        // Remove elements we don't want in the capture
        contentClone.querySelectorAll('.download-ignore').forEach(el => el.remove());
        
        // Add to temp container
        tempContainer.appendChild(contentClone);
        
        // Add to document but position off-screen
        tempContainer.style.position = 'fixed';
        tempContainer.style.top = '-9999px';
        tempContainer.style.left = '0';
        tempContainer.style.zIndex = '-1000';
        document.body.appendChild(tempContainer);
        
        try {
          const canvas = await html2canvas(tempContainer, {
            scale: window.devicePixelRatio * 2,
            useCORS: true,
            backgroundColor: '#0f172a', // Solid background
            logging: true,
            ignoreElements: (element) => {
              return element.classList && element.classList.contains('download-ignore');
            }
          });
          
          // Create download link
          const link = document.createElement('a');
          link.download = 'gustify-wrapped.png';
          link.href = canvas.toDataURL('image/png');
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
          
        } catch (error) {
          console.error('Error generating image:', error);
          alert('Error generating image. Please try again.');
        } finally {
          // Clean up temporary container
          document.body.removeChild(tempContainer);
        }
      }
    </script>
</x-app-layout>