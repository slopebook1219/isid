<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Title -->
        <h1 class="text-3xl font-bold !text-black dark:!text-white mb-4 text-center">ログイン</h1>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="メールアドレス" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="パスワード" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">
                    ログイン情報を保存する
                </span>
            </label>
        </div>

        <!-- Login Button -->
        <div class="mt-4">
            <button type="submit" class="w-full justify-center py-3 bg-lime-500 hover:bg-lime-600 text-white
                 inline-flex items-center px-4 font-semibold rounded-md shadow-sm
                 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500">
                ログイン
            </button>
        </div>

        <!-- Forgot Password -->
        @if (Route::has('password.request'))
            <div class="mt-4 text-center">
                <a class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    パスワードを忘れた方はこちら
                </a>
            </div>
        @endif

        <!-- Separator line -->
        <div class="my-6">
            <hr class="border-gray-300 dark:border-gray-550">
        </div>

        <!-- Register link -->
        @if (Route::has('register'))
            <div class="text-center mb-6">
                <a href="{{ route('register') }}"
                class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200">
                    新規登録はこちら
                </a>
            </div>
        @endif
    </form>
</x-guest-layout>
