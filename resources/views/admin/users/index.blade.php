@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">User Management</h2>

    {{-- Success & Error Alerts --}}
    @if (session('success'))
        <div class="mb-4 text-green-700 bg-green-100 border border-green-300 px-4 py-2 rounded">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="mb-4 text-red-700 bg-red-100 border border-red-300 px-4 py-2 rounded">
            {{ session('error') }}
        </div>
    @endif

    {{-- Responsive Card Row --}}
 <div class="flex flex-wrap gap-4 justify-start mb-6">


                {{-- CARD: Total Jobs --}}
        <div class="flex-1 min-w-[220px] max-w-[220px] bg-white border border-gray-200 rounded-lg shadow-sm p-4">
            <div class="text-sm text-gray-500 text-center mb-1">Total Jobs</div>
            <div class="flex justify-center items-center">
                <svg data-lucide="briefcase" class="w-6 h-6 text-blue-600"></svg>
                <span class="ml-3 text-2xl font-bold text-gray-800">{{ $totalJobs }}</span>
            </div>
        </div>

        {{-- CARD: Total Students --}}
        <div class="flex-1 min-w-[220px] max-w-[220px] bg-white border border-gray-200 rounded-lg shadow-sm p-4">
            <div class="text-sm text-gray-500 text-center mb-1">Total Students</div>
            <div class="flex justify-center items-center">
                <svg data-lucide="graduation-cap" class="w-6 h-6 text-yellow-500"></svg>
                <span class="ml-3 text-2xl font-bold text-gray-800">{{ $totalStudents }}</span>
            </div>
        </div>

        {{-- CARD: Total Employers --}}
        <div class="flex-1 min-w-[220px] max-w-[220px] bg-white border border-gray-200 rounded-lg shadow-sm p-4">
            <div class="text-sm text-gray-500 text-center mb-1">Total Employers</div>
            <div class="flex justify-center items-center">
                <svg data-lucide="users" class="w-6 h-6 text-green-600"></svg>
                <span class="ml-3 text-2xl font-bold text-gray-800">{{ $totalEmployers }}</span>
            </div>
        </div>

        {{-- CARD: Total Applications --}}
        <div class="flex-1 min-w-[220px] max-w-[220px] bg-white border border-gray-200 rounded-lg shadow-sm p-4">
            <div class="text-sm text-gray-500 text-center mb-1">Total Applications</div>
            <div class="flex justify-center items-center">
                <svg data-lucide="file-text" class="w-6 h-6 text-purple-600"></svg>
                <span class="ml-3 text-2xl font-bold text-gray-800">{{ $totalApplications }}</span>
            </div>
        </div>

    </div>

    {{-- Filter Bar --}}
    <div class="mt-4 mb-6 bg-white border border-gray-200 shadow-sm rounded-lg p-4">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col md:flex-row md:items-center gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or email"
                class="w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:border-blue-300">

            <select name="role" class="w-full md:w-1/4 px-3 py-2 border border-gray-300 rounded-lg">
                <option value="">All Roles</option>
                <option value="student" {{ request('role') == 'student' ? 'selected' : '' }}>Student</option>
                <option value="employer" {{ request('role') == 'employer' ? 'selected' : '' }}>Employer</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>

            <div class="flex gap-2">
                <button type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">Filter</button>
                <a href="{{ route('admin.users.index') }}"
                    class="bg-gray-100 text-gray-800 px-4 py-2 rounded-lg border hover:bg-gray-200 transition">Reset</a>
            </div>
        </form>
    </div>

    {{-- Users Table --}}
    <div class="bg-white border border-gray-200 shadow-sm rounded-lg overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-700">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr class="text-xs font-semibold text-gray-600 uppercase">
                    <th class="px-6 py-4">#</th>
                    <th class="px-6 py-4">Name</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Role</th>
                    <th class="px-6 py-4">Registered At</th>
                    <th class="px-6 py-4">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($users as $index => $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 font-medium">{{ $user->name }}</td>
                        <td class="px-6 py-4">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-block px-2 py-1 text-xs rounded-full font-semibold
                                {{ $user->role === 'admin' ? 'bg-blue-100 text-blue-700' : 
                                   ($user->role === 'employer' ? 'bg-green-100 text-green-700' : 
                                   'bg-yellow-100 text-yellow-700') }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $user->created_at->format('Y-m-d') }}</td>
                        <td class="px-6 py-4">
                            @if ($user->role !== 'admin')
                                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                                    onsubmit="return confirm('Delete this user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-600 hover:text-red-800 font-medium text-sm">Delete</button>
                                </form>
                            @else
                                <span class="text-gray-400 italic text-sm">Admin</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center px-6 py-8 text-gray-500">No users found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
