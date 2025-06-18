@php $user = Auth::user(); @endphp

<section>
    <header>
        <h2 class="text-lg font-semibold text-gray-800">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        {{-- Name --}}
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
            <input id="name" name="name" type="text"
                   value="{{ old('name', $user->name) }}"
                   required
                   autocomplete="name"
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded bg-white text-gray-900 placeholder-gray-400">
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input id="email" name="email" type="email"
                   value="{{ old('email', $user->email) }}"
                   required
                   autocomplete="username"
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded bg-white text-gray-900 placeholder-gray-400">
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-sm text-gray-600">
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification"
                                class="underline text-sm text-indigo-600 hover:text-indigo-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Phone Number --}}
        <div>
            <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
            <input id="phone_number" name="phone_number" type="text"
                   value="{{ old('phone_number', $user->phone_number) }}"
                   autocomplete="tel"
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded bg-white text-gray-900 placeholder-gray-400">
            <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
        </div>

        {{-- Company Name --}}
        @if($user->role === 'employer')
            <div>
                <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name</label>
                <input id="company_name" name="company_name" type="text"
                       value="{{ old('company_name', $user->company_name) }}"
                       autocomplete="organization"
                       class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded bg-white text-gray-900 placeholder-gray-400">
                <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
            </div>
        @endif

        {{-- Address --}}
        <div>
            <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
            <input id="address" name="address" type="text"
                   value="{{ old('address', $user->address) }}"
                   autocomplete="street-address"
                   class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded bg-white text-gray-900 placeholder-gray-400">
            <x-input-error class="mt-2" :messages="$errors->get('address')" />
        </div>

        {{-- Save Button --}}
        <div class="flex items-center gap-4">
            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 transition">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }"
                   x-show="show"
                   x-transition
                   x-init="setTimeout(() => show = false, 2000)"
                   class="text-sm text-gray-600">
                    {{ __('Saved.') }}
                </p>
            @endif
        </div>
    </form>
</section>
