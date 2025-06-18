<x-guest-layout>
    <div class="text-center mb-6">
        <div class="icon-circle">🏢</div>
        <h2 class="card-title">Sign Up as Employer</h2>
        <p class="card-description">Create an account to post part-time job opportunities</p>
    </div>

    <form method="POST" action="{{ route('register.employer') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Your Name</label>
            <input type="text" id="name" name="name" value="{{ old('name') }}" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @error('name')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @error('email')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Company Name -->
        <div>
            <label for="company_name" class="block text-sm font-medium text-gray-700">Company Name</label>
            <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @error('company_name')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Phone Number -->
        <div>
            <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
            <div class="flex rounded-md shadow-sm">
                <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
                    +60
                </span>
                <input type="tel" name="phone_number" id="phone_number"
                    value="{{ old('phone_number') }}"
                    pattern="[0-9]{9,10}" placeholder="123456789" required
                    class="mt-0 w-full border border-gray-300 rounded-r-md focus:ring focus:ring-blue-200">
            </div>
            <p class="text-xs text-gray-500 mt-1">Enter without leading 0. Example: <code>123456789</code></p>
            @error('phone_number')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Address -->
        <div>
            <label for="address" class="block text-sm font-medium text-gray-700">Company Address</label>
            <textarea id="address" name="address" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('address') }}</textarea>
            @error('address')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" id="password" name="password" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
            @error('password')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required class="mt-1 w-full border-gray-300 rounded-md shadow-sm">
        </div>

        <!-- Register Button -->
        <button type="submit" class="btn bg-blue-600 text-white w-full py-2 rounded hover:bg-blue-700 font-semibold">
            Register
        </button>

        <p class="text-sm text-center text-gray-600 mt-3">
            Already have an account?
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Login</a>
        </p>

            <div class="mt-2 text-center">
            <a href="{{ route('job.search') }}" class="text-blue-600 hover:underline text-sm font-medium inline-block">
                ← Back to Home
            </a>
        </div>


    </form>
</x-guest-layout>
