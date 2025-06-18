<x-guest-layout>
    <div class="text-center mb-6">
        <div class="icon-circle">🎓</div>
        <h2 class="card-title">Join as Student</h2>
        <p class="card-description">Create your student account to find part-time jobs</p>
    </div>

    <form method="POST" action="{{ route('register.student') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm" />
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-sm text-red-600" />
        </div>

        <!-- Phone Number -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Phone Number</label>
            <div class="flex rounded-md shadow-sm">
                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                    +60
                </span>
                <input 
                    type="tel" 
                    name="phone_number" 
                    pattern="[0-9]{9,10}" 
                    placeholder="123456789" 
                    value="{{ old('phone_number') }}"
                    required 
                    class="mt-0 w-full border border-gray-300 rounded-r-md focus:ring focus:ring-blue-200" 
                />
            </div>
            <p class="text-xs text-gray-500 mt-1">Enter your phone number without the leading 0. Example: <code>123456789</code></p>
            <x-input-error :messages="$errors->get('phone_number')" class="mt-2 text-sm text-red-600" />
        </div>

        <!-- Email -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
        </div>

        <!-- Address -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Address</label>
            <textarea name="address" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('address') }}</textarea>
            <x-input-error :messages="$errors->get('address')" class="mt-2 text-sm text-red-600" />
        </div>

        <!-- Password -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" name="password" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input type="password" name="password_confirmation" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm text-red-600" />
        </div>

        <!-- Submit -->
        <button type="submit" class="btn bg-blue-600 text-white w-full py-2 rounded hover:bg-blue-700 font-semibold">
            Create Account
        </button>

        <p class="text-sm text-center text-gray-600 mt-3">
            Already have an account?
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Sign in</a>
        </p>
    </form>
</x-guest-layout>
