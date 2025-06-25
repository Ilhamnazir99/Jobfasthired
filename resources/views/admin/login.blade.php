<x-guest-layout>
    <!-- Header -->
    <div class="card-header text-center mb-4">
        <h2 class="text-2xl font-bold text-blue-700">Welcome Admin</h2>
        <p class="text-gray-500">Sign in to manage JobFastHired system</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Error Message -->
    @if($errors->any())
        <div class="bg-red-100 border border-red-300 text-red-600 text-sm p-3 rounded mb-4">
            {{ $errors->first() }}
        </div>
    @endif

    <!-- Login Form -->
    <form method="POST" action="{{ url('/admin/login') }}" class="space-y-4">
        @csrf

        <!-- Email -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring focus:ring-blue-200" />
            <x-input-error :messages="$errors->get('email')" class="mt-1 text-red-500 text-sm" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" id="password" name="password" required
                class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring focus:ring-blue-200" />
            <x-input-error :messages="$errors->get('password')" class="mt-1 text-red-500 text-sm" />
        </div>

        <!-- Remember me -->
        <div class="flex items-center">
            <input type="checkbox" name="remember" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" />
            <label class="ml-2 text-sm text-gray-600">Remember me</label>
        </div>

        <!-- Submit -->
        <div>
            <button type="submit"
                class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition">
                Login as Admin
            </button>
        </div>
    </form>

    <!-- Back to home -->
    <div class="text-center mt-4">
        <a href="/" class="text-sm text-gray-500 hover:underline">← Back to Home</a>
    </div>
</x-guest-layout>
