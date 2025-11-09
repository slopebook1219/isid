<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Title -->
        <h1 class="text-2xl sm:text-3xl font-bold text-black text-center">
            ログイン
        </h1>

        <!-- Email Address -->
        <div class="space-y-2">
            <x-input-label for="email" value="メールアドレス" />
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <x-input-label for="password" value="パスワード" />
            <x-text-input id="password" class="block w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <!-- Remember Me -->
        <div>
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-600">
                    ログイン情報を保存する
                </span>
            </label>
        </div>

        <!-- Login Button -->
        <div>
            <button type="submit" class="w-full justify-center py-3 bg-lime-500 hover:bg-lime-600 text-white
                 inline-flex items-center px-4 font-semibold rounded-md shadow-sm
                 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500">
                ログイン
            </button>
        </div>

        <!-- Forgot Password -->
        <div class="text-center space-y-6">
            @if (Route::has('password.request'))
                <div>
                    <a class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-500 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                        パスワードを忘れた方はこちら
                    </a>
                </div>
            @endif

            <!-- Separator line -->
            <hr class="border-gray-300 dark:border-gray-700">

            <!-- Register link -->
            @if (Route::has('register'))
                <div>
                    <a href="{{ route('register') }}"
                    class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-500">
                        新規登録はこちら
                    </a>
                </div>
            @endif
        </div>
    </form>
</x-guest-layout>
