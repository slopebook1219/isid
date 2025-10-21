<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Title -->
        <h1 class="text-3xl font-bold !text-black mb-4 text-center">新規登録</h1>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" value="メールアドレス" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="パスワード" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="パスワード(確認用)" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Register Button -->
        <div class="mt-4">
            <button type="submit" class="w-full justify-center py-3 bg-lime-500 hover:bg-lime-600 text-white
                 inline-flex items-center px-4 font-semibold rounded-md shadow-sm
                 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lime-500">
                登録
            </button>
        </div>

        <!-- Forgot Password -->
        @if (Route::has('password.request'))
            <div class="mt-4 text-center">
                <a class="text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                    パスワードを忘れた方はこちら
                </a>
            </div>
        @endif

        <!-- Separator line -->
        <hr class="my-6 border-gray-300">

        <!-- Login link -->
        @if (Route::has('login'))
            <div class="text-center">
                <a href="{{ route('login') }}"
                   class="text-sm text-gray-600 hover:text-gray-900">
                    すでにアカウントをお持ちの方はこちら
                </a>
            </div>
        @endif
    </form>
</x-guest-layout>
