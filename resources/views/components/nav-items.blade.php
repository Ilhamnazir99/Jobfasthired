<!-- GUEST LINKS -->
@guest
    <a href="{{ route('job.search') }}"
       class="nav-link block sm:inline-block border-b-2 border-transparent hover:border-blue-600 {{ request()->is('job-search') ? 'border-blue-600 text-blue-700 font-semibold' : '' }}">
        Job Search
    </a>
    <a href="{{ route('login') }}"
       class="nav-link block sm:inline-block border-b-2 border-transparent hover:border-blue-600 {{ request()->is('login') ? 'border-blue-600 text-blue-700 font-semibold' : '' }}">
        Login
    </a>

    <!-- Register Dropdown (Styled like before) -->
    <div x-data="{ openRegister: false }" class="relative block sm:inline-block">
        <button @click="openRegister = !openRegister"
                class="nav-link flex items-center border-b-2 border-transparent hover:border-blue-600">
            Register
            <svg data-lucide="chevron-down" class="ms-1 w-4 h-4"></svg>
        </button>
        <div x-show="openRegister"
             x-transition
             @click.away="openRegister = false"
             class="absolute right-0 mt-2 w-full sm:w-48 bg-white border border-gray-200 rounded shadow-lg z-50 overflow-hidden">
           <a href="{{ url('/register/student') }}"
   class="dropdown-item hover:bg-gray-100 px-4 py-2 block text-sm text-gray-700 flex items-center gap-2">
    <svg data-lucide="graduation-cap" class="w-4 h-4 text-blue-600"></svg>
    Sign up as Student
</a>
<a href="{{ url('/register/employer') }}"
   class="dropdown-item hover:bg-gray-100 px-4 py-2 block text-sm text-gray-700 flex items-center gap-2">
    <svg data-lucide="briefcase" class="w-4 h-4 text-green-600"></svg>
    Sign up as Employer
</a>

        </div>
    </div>
@endguest

<!-- AUTH LINKS -->
@auth
    @php
        $role = Auth::user()->role;
        $profileImage = Auth::user()->profile_image ? asset('images/' . Auth::user()->profile_image) : asset('images/default-avatar.png');
    @endphp

    @if($role === 'admin')
        <a href="{{ route('admin.dashboard') }}"
           class="nav-link border-b-2 border-transparent hover:border-blue-600 {{ request()->is('admin/dashboard*') ? 'border-blue-600 text-blue-700 font-semibold' : '' }}">
            Dashboard
        </a>
    @elseif($role === 'employer')
        <a href="{{ route('employer.dashboard') }}"
           class="nav-link border-b-2 border-transparent hover:border-blue-600 {{ request()->is('employer/dashboard*') ? 'border-blue-600 text-blue-700 font-semibold' : '' }}">
            Dashboard
        </a>
        <a href="{{ route('job.create') }}"
           class="nav-link border-b-2 border-transparent hover:border-blue-600 {{ request()->is('job/create*') ? 'border-blue-600 text-blue-700 font-semibold' : '' }}">
            Post Job
        </a>
        <a href="{{ route('employer.notifications') }}"
           class="relative nav-link flex items-center border-b-2 border-transparent hover:border-blue-600 {{ request()->is('employer/notifications') ? 'border-blue-600 text-blue-700 font-semibold' : '' }}">
            <svg data-lucide="bell" class="w-5 h-5 stroke-current"></svg>
            @if(Auth::user()->unreadNotifications->count())
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full animate-pulse">
                    {{ Auth::user()->unreadNotifications->count() }}
                </span>
            @endif
        </a>
    @elseif($role === 'student')
        <a href="{{ route('job.search') }}"
           class="nav-link border-b-2 border-transparent hover:border-blue-600 {{ request()->is('student/dashboard*') ? 'border-blue-600 text-blue-700 font-semibold' : '' }}">
            Job Search
        </a>
        <a href="{{ route('student.applied.jobs') }}"
           class="nav-link border-b-2 border-transparent hover:border-blue-600 {{ request()->is('student/applied*') ? 'border-blue-600 text-blue-700 font-semibold' : '' }}">
            Applied
        </a>
        <a href="{{ route('student.notifications') }}"
           class="relative nav-link flex items-center border-b-2 border-transparent hover:border-blue-600 {{ request()->is('student/notifications') ? 'border-blue-600 text-blue-700 font-semibold' : '' }}">
            <svg data-lucide="bell" class="w-5 h-5 stroke-current"></svg>
            @if(Auth::user()->unreadNotifications->count())
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full animate-pulse">
                    {{ Auth::user()->unreadNotifications->count() }}
                </span>
            @endif
        </a>
    @endif

    <!-- Profile Dropdown -->
    <div class="mt-2 sm:mt-0">
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                <button class="flex items-center gap-2 hover:text-blue-700 transition">
                    <img src="{{ $profileImage }}" alt="Profile Image"
                         class="w-8 h-8 rounded-full object-cover border border-gray-300 shadow-sm">
                    <span class="text-sm font-medium text-gray-700 hidden sm:block">{{ Auth::user()->name }}</span>
                    <svg data-lucide="chevron-down" class="w-4 h-4 text-gray-500"></svg>
                </button>
            </x-slot>
            <x-slot name="content">
                <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link :href="route('logout')"
                                     onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
@endauth
