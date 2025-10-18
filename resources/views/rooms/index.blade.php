<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            ルーム一覧ページです
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center">
                    <p class="text-white text-2xl">
                        何チームで行いますか？
                    </p>
                </div>
            </div>
        </div>
    </div>
    <a href="{{ route('rooms.create') }}" 
            class="inline-block bg-indigo-600 text-black font-semibold py-2 px-4 rounded-lg shadow-md hover:bg-indigo-700 transition duration-150 ease-in-out">
            ルーム作成はこちら
</x-app-layout>