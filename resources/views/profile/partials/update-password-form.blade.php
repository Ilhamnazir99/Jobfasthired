<section>
    <header>
        <h2 class="text-lg font-semibold text-gray-800">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        {{-- Current Password --}}
        <div>
            <label for="update_password_current_password" class="block text-sm font-medium text-gray-700">
                {{ __('Current Password') }}
            </label>
            <input id="update_password_current_password" name="current_password" type="password"
                   autocomplete="current-password"
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded bg-white text-gray-900 placeholder-gray-400" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        {{-- New Password --}}
        <div>
            <label for="update_password_password" class="block text-sm font-medium text-gray-700">
                {{ __('New Password') }}
            </label>
            <input id="update_password_password" name="password" type="password"
                   autocomplete="new-password"
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded bg-white text-gray-900 placeholder-gray-400" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        {{-- Confirm Password --}}
        <div>
            <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700">
                {{ __('Confirm Password') }}
            </label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                   autocomplete="new-password"
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded bg-white text-gray-900 placeholder-gray-400" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        {{-- Save Button --}}
        <div class="flex items-center gap-4">
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 transition">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
