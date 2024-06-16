<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <div class="text-center">
                <h1 class="block text-2xl font-bold text-gray-800 dark:text-white">{{ __('Sign in') }}</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-neutral-400">
                  {{ __("Don't have an account yet?") }}
                  @if (Route::has('register'))
                  <a class="text-blue-600 decoration-2 hover:underline font-medium dark:text-blue-500" href="{{ route('register') }}">
                    {{ __('Sign up here') }}
                  </a>
                  @endif
                </p>
              </div>

        </x-slot>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
            </div>



            <div class="flex items-center justify-between gap-x-3 my-5">
                <div>
                    <label for="remember_me" class="flex items-center">
                        <x-checkbox id="remember_me" name="remember" />
                        <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                    </label>
                </div>

                @if (Route::has('password.request'))
                    <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

            </div>

            <x-button class="w-full ">
                    {{ __('Sign in') }}
            </x-button>
        </form>
    </x-authentication-card>
</x-guest-layout>
