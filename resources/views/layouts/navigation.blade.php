<nav x-data="{ sidebarOpen: false }" class="bg-white border-b shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            <!-- Left Section: Hamburger + Logo -->
            <div class="flex items-center gap-3">
                <!-- Hamburger (Mobile) -->
                <div class="sm:hidden">
                    <button @click="sidebarOpen = true" class="text-gray-600 hover:text-blue-600 focus:outline-none">
                        <svg data-lucide="menu" class="w-6 h-6"></svg>
                    </button>
                </div>

                <!-- Logo -->
                <a href="{{ auth()->check() ? (Auth::user()->role == 'employer' ? route('employer.dashboard') : (Auth::user()->role == 'student' ? route('job.search') : route('admin.dashboard'))) : route('job.search') }}"
                   class="text-xl font-bold text-blue-600 hover:text-blue-800 transition duration-300">
                    JobFastHired
                </a>
            </div>

            <!-- Desktop Links (Right side) -->
            <div class="hidden sm:flex gap-6 items-center">
                @include('components.nav-items') <!-- Reuse your nav logic -->
            </div>
        </div>
    </div>

    <!-- MOBILE SIDEBAR -->
    <div x-show="sidebarOpen" class="fixed inset-0 z-50 flex sm:hidden" x-transition>
        <!-- Overlay -->
        <div class="fixed inset-0 bg-black bg-opacity-40" @click="sidebarOpen = false"></div>

        <!-- Sidebar Content -->
        <div class="relative w-64 bg-white shadow-lg h-full p-5 overflow-y-auto z-50">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-bold text-blue-600 flex items-center gap-1">
                    <svg data-lucide="menu" class="w-5 h-5"></svg> Menu
                </h2>
                <button @click="sidebarOpen = false" class="text-gray-600 hover:text-red-500">
                    <svg data-lucide="x" class="w-5 h-5"></svg>
                </button>
            </div>

            <!-- Links -->
            <div class="space-y-4">
                @guest
                    <a href="{{ route('job.search') }}" class="side-link flex items-center gap-2">
                        <svg data-lucide="search" class="w-4 h-4 text-blue-500"></svg>
                        Job Search
                    </a>
                    <a href="{{ route('login') }}" class="side-link flex items-center gap-2">
                        <svg data-lucide="log-in" class="w-4 h-4 text-green-600"></svg>
                        Login
                    </a>
                    <div>
                    <p class="text-xs font-semibold text-gray-400 mb-1">Register As</p>
                    <a href="{{ url('/register/student') }}" class="side-sublink block flex items-center gap-2">
                        <svg data-lucide="graduation-cap" class="w-4 h-4 text-blue-600"></svg>
                        Student
                    </a>
                    <a href="{{ url('/register/employer') }}" class="side-sublink block flex items-center gap-2">
                        <svg data-lucide="briefcase" class="w-4 h-4 text-green-600"></svg>
                        Employer
                    </a>
                </div>

                @endguest

                @auth
                    @php
                        $role = Auth::user()->role;
                        $profileImage = Auth::user()->profile_image ? asset('images/' . Auth::user()->profile_image) : asset('images/default-profile.png');
                    @endphp

                    @if($role === 'admin')
                        <a href="{{ route('admin.dashboard') }}" class="side-link flex items-center gap-2">
                            <svg data-lucide="layout-dashboard" class="w-4 h-4"></svg>
                            Admin Dashboard
                        </a>
                    @elseif($role === 'employer')
                        <a href="{{ route('employer.dashboard') }}" class="side-link flex items-center gap-2">
                            <svg data-lucide="briefcase" class="w-4 h-4"></svg>
                            Dashboard
                        </a>
                        <a href="{{ route('job.create') }}" class="side-link flex items-center gap-2">
                            <svg data-lucide="plus-circle" class="w-4 h-4"></svg>
                            Post Job
                        </a>
                        <a href="{{ route('employer.notifications') }}" class="side-link flex items-center gap-2 relative">
                            <svg data-lucide="bell" class="w-4 h-4"></svg>
                            Notifications
                            @if(Auth::user()->unreadNotifications->count())
                                <span class="absolute -top-1 right-0 bg-red-600 text-white text-[10px] px-1.5 py-0.5 rounded-full animate-pulse">
                                    {{ Auth::user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </a>
                    @elseif($role === 'student')
                        <a href="{{ route('student.dashboard') }}" class="side-link flex items-center gap-2">
                            <svg data-lucide="home" class="w-4 h-4"></svg>
                            Job Search
                        </a>
                        <a href="{{ route('student.applied.jobs') }}" class="side-link flex items-center gap-2">
                            <svg data-lucide="file-text" class="w-4 h-4"></svg>
                            My Applications
                        </a>
                        <a href="{{ route('student.notifications') }}" class="side-link flex items-center gap-2 relative">
                            <svg data-lucide="bell" class="w-4 h-4"></svg>
                            Notifications
                            @if(Auth::user()->unreadNotifications->count())
                                <span class="absolute -top-1 right-0 bg-red-600 text-white text-[10px] px-1.5 py-0.5 rounded-full animate-pulse">
                                    {{ Auth::user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </a>
                    @endif

                    <!-- Profile Footer -->
                    <div class="border-t pt-4 mt-4">
                        <div class="flex items-center gap-3">
                            <img src="{{ $profileImage }}" class="w-8 h-8 rounded-full object-cover border">
                            <span class="text-sm text-gray-700 font-medium">{{ Auth::user()->name }}</span>
                        </div>
                        <a href="{{ route('profile.edit') }}" class="side-sublink flex items-center gap-1 mt-3">
                            <svg data-lucide="settings" class="w-4 h-4"></svg>
                            Profile
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="mt-2">
                            @csrf
                            <button type="submit" class="side-sublink text-red-600 flex items-center gap-1">
                                <svg data-lucide="log-out" class="w-4 h-4"></svg>
                                Log Out
                            </button>
                        </form>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>

<!-- Lucide Icons Init -->
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        lucide.createIcons();
    });
</script>
