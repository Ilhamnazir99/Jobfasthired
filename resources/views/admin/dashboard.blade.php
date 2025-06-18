@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold mb-4">Welcome, Admin!</h1>
    <p class="mb-6">This is the admin dashboard.</p>

    <div class="flex flex-col md:flex-row gap-4">
        {{-- Manage Users --}}
        <a href="{{ route('admin.users.index') }}" 
           class="bg-blue-600 text-white px-5 py-3 rounded-lg shadow hover:bg-blue-700 transition">
            👥 Manage Users
        </a>

        {{-- Manage Jobs --}}
        <a href="{{ route('admin.jobs.index') }}" 
           class="bg-green-600 text-white px-5 py-3 rounded-lg shadow hover:bg-green-700 transition">
            📝 Manage Jobs
        </a>
    </div>
</div>
@endsection
