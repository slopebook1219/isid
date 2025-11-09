<x-app-layout>
    <main class="items-center justify-center h-screen bg-white">
        <div class="flex flex-col mx-auto w-[60%] h-full">
            <div class="flex h-1/2 items-end justify-center gap-8">
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
            </div>
            <div class="h-1/2 flex justify-center items-start mt-8">
                <a class="flex justify-center items-center h-64
                          bg-lime-500 text-white
                          rounded-2xl shadow-lg
                          transition-all duration-300
                          transform hover:-translate-y-1 hover:shadow-2xl
                          focus:outline-none focus:ring-2 focus:ring-lime-700 focus:ring-opacity-50
                          w-[calc(2*20rem+2rem)] sm:w-[calc(2*20rem+2rem)]"
                   href="{{ route('games.create') }}">
                    <span class="text-4xl font-bold">ゲーム作成</span>
                </a>
            </div>
        </div>
    </main>
</x-app-layout>
