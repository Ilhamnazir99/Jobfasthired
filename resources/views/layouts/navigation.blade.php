<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo -->
                <a href="{{ route('job.search') }}" class="text-xl font-bold text-blue-600">JobFastHired</a>
            </div>

            <div class="hidden sm:flex sm:items-center space-x-4">
                <!-- Guest Navigation -->
                @guest
                    <a href="{{ route('job.search') }}" class="text-gray-600 hover:text-blue-600">Job Search</a>
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600">Login</a>

                    <!-- Register Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="text-gray-600 hover:text-blue-600 flex items-center">
                            Register
                            <svg class="ms-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414L10 13.414 5.293 8.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white border rounded shadow-md z-50">
                            <a href="{{ url('/register/student') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign up as Student</a>
                            <a href="{{ url('/register/employer') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sign up as Employer</a>
                        </div>
                    </div>
                @endguest

                <!-- Authenticated User Navigation -->
                @auth
                    @if(Auth::user()->role == 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-blue-600">Admin Dashboard</a>

                    @elseif(Auth::user()->role == 'employer')
                        <a href="{{ route('employer.dashboard') }}" class="text-gray-600 hover:text-blue-600">Employer Dashboard</a>
                        <a href="{{ route('job.create') }}" class="text-gray-600 hover:text-blue-600">Post Job</a>

                    @elseif(Auth::user()->role == 'student')
                        <a href="{{ route('student.dashboard') }}" class="text-gray-600 hover:text-blue-600">Dashboard</a>
                        <a href="{{ route('student.applied.jobs') }}" class="text-gray-600 hover:text-blue-600">Applied Jobs</a>
                        <a href="{{ route('student.notifications') }}" class="text-gray-600 hover:text-blue-600">Notifications</a>
                        <a href="{{ route('profile.edit') }}" class="text-gray-600 hover:text-blue-600">Profile</a>
                    @endif

                    <!-- User Profile & Logout -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="flex items-center text-gray-600 hover:text-blue-600">
                                <span>{{ Auth::user()->name }}</span>
                                <svg class="ms-2 w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7M5 10v10a1 1 0 001 1h3m10-11v10a1 1 0 01-1 1h-3m-6-8h4m-4 4h4" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                                 onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @endauth
            </div>
        </div>
    </div>
</nav>
