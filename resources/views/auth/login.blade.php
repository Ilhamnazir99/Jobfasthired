<x-guest-layout>
    <!-- Show session status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="card-header text-center mb-4">
        <h2 class="text-2xl font-bold">Welcome Back</h2>
        <p class="text-gray-500">Login to your JobFasthired account</p>
    </div>

    <!-- Tabs -->
    <div class="tabs-list flex justify-center space-x-4 mb-4">
        <button class="tab-button active px-4 py-2 border rounded" data-tab="student">Student</button>
        <button class="tab-button px-4 py-2 border rounded" data-tab="employer">Employer</button>
    </div>

    <!-- Student Login Form -->
    <div id="student" class="tabs-content" style="display: block;">
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- ADDED: Role for student -->
            <input type="hidden" name="role" value="student"> 

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
                    Login as Student
                </button>
            </div>
        </form>

        <div class="text-center mt-4 text-sm">
            Don't have an account? <a href="{{ route('register.student.form') }}" class="text-blue-600 hover:underline">Sign up as Student</a>
        </div>

        @if (Route::has('password.request'))
            <div class="text-center mt-2">
                <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline">Forgot your password?</a>
            </div>
        @endif

        <!-- Back to home (inside white box) -->
        <div class="text-center mt-4">
            <a href="/" class="text-sm text-gray-500 hover:underline">← Back to Home</a>
        </div>
    </div>

    <!-- Employer Login Form -->
    <div id="employer" class="tabs-content" style="display: none;">
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <!-- ADDED: Role for employer -->
            <input type="hidden" name="role" value="employer"> 

            <!-- Email -->
            <div>
                <label for="employer_email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="employer_email" name="email" value="{{ old('email') }}" required
                    class="mt-1 block w-full px-3 py-2 border rounded-md shadow-sm focus:ring focus:ring-blue-200" />
                <x-input-error :messages="$errors->get('email')" class="mt-1 text-red-500 text-sm" />
            </div>

            <!-- Password -->
            <div>
                <label for="employer_password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="employer_password" name="password" required
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
                    Login as Employer
                </button>
            </div>
        </form>

        <div class="text-center mt-4 text-sm">
            Don't have an account? <a href="{{ route('register.employer.form') }}" class="text-blue-600 hover:underline">Sign up as Employer</a>
        </div>

        @if (Route::has('password.request'))
            <div class="text-center mt-2">
                <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:underline">Forgot your password?</a>
            </div>
        @endif

        <!-- Back to home (inside white box) -->
        <div class="text-center mt-4">
            <a href="/" class="text-sm text-gray-500 hover:underline">← Back to Home</a>
        </div>
    </div>

    <!-- Tabs Script -->
    <script>
        const buttons = document.querySelectorAll('.tab-button');
        const contents = document.querySelectorAll('.tabs-content');
        buttons.forEach(btn => {
            btn.addEventListener('click', () => {
                buttons.forEach(b => b.classList.remove('active'));
                contents.forEach(c => c.style.display = 'none');
                btn.classList.add('active');
                document.getElementById(btn.dataset.tab).style.display = 'block';
            });
        });
    </script>
</x-guest-layout>
