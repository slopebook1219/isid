<x-app-layout>
    <!-- Main Content -->
    <main class="flex items-center justify-center min-h-screen px-8 py-12 bg-white">
        <div class="flex flex-wrap justify-center items-center gap-10">
            
            <a class="flex justify-center items-center w-64 h-64 sm:w-80 sm:h-80  
                      bg-lime-500 text-white
                      rounded-2xl shadow-lg
                      transition-all duration-300 
                      transform hover:-translate-y-1 hover:shadow-2xl
                      focus:outline-none focus:ring-2 focus:ring-lime-700 focus:ring-opacity-50"
                      href="{{ route('questions.index') }}">
                <span class="text-4xl font-bold">問題</span>
            </a>

            <a class="flex justify-center items-center w-64 h-64 sm:w-80 sm:h-80
                      bg-lime-500 text-white
                      rounded-2xl shadow-lg 
                      transition-all duration-300 
                      transform hover:-translate-y-1 hover:shadow-2xl
                      focus:outline-none focus:ring-2 focus:ring-lime-700 focus:ring-opacity-50"
                      href="{{ route('rooms.index') }}">
                <span class="text-4xl font-bold">ルーム</span>
            </a>

            <a class="flex justify-center items-center w-64 h-64 sm:w-80 sm:h-80
                      bg-lime-500 text-white
                      rounded-2xl shadow-lg 
                      transition-all duration-300 
                      transform hover:-translate-y-1 hover:shadow-2xl
                      focus:outline-none focus:ring-2 focus:ring-lime-700 focus:ring-opacity-50"
                      href="{{ route('games.create') }}">
                <span class="text-4xl font-bold">ゲーム作成</span>
            </a>

        </div>
    </main>
</x-app-layout>