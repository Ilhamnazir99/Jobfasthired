@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-5 px-4">
<div class="flex items-center justify-between mb-6">
    {{-- Welcome Message --}}
    <h2 class="text-xl font-semibold text-gray-800">
        Welcome, {{ auth()->user()->name }}!
    </h2>

    {{-- Post New Job + Notification --}}
    <div class="flex items-center gap-4">
        <a href="{{ route('job.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded flex items-center gap-2">
            <svg data-lucide="plus-circle" class="w-4 h-4"></svg> Post a New Job
        </a>

        {{-- Notification Bell --}}
        <div class="relative">
            <button id="notification-btn" class="relative bg-gray-200 hover:bg-gray-300 rounded-full p-2 focus:outline-none">
                <svg data-lucide="bell" class="w-5 h-5"></svg>
                <span id="notification-count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-1.5">
                    {{ auth()->user()->unreadNotifications->count() }}
                </span>
            </button>

            {{-- Notifications Dropdown --}}
            <div id="notification-dropdown" class="hidden absolute right-0 mt-2 w-72 bg-white shadow-lg rounded overflow-hidden z-10">
                <div class="p-3 font-bold border-b">Notifications</div>
                <div class="max-h-64 overflow-y-auto">
                    @forelse (auth()->user()->unreadNotifications as $notification)
                        <div class="p-3 border-b hover:bg-gray-100">
                            <p class="text-sm">
                                <strong>{{ $notification->data['student_name'] }}</strong> applied for 
                                <strong>{{ $notification->data['job_title'] }}</strong>.
                            </p>
                            <div class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</div>
                            <form action="{{ route('employer.markNotification', $notification->id) }}" method="POST" class="mt-1">
                                @csrf
                                <button class="text-blue-500 text-sm hover:underline">Mark as Read</button>
                            </form>
                        </div>
                    @empty
                        <div class="p-3 text-sm text-gray-500">No new notifications.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

    {{-- Job Summary Cards --}}
{{-- Summary Cards Centered --}}
<div class="flex justify-center flex-wrap gap-4 mb-10">
    {{-- Active Jobs Card --}}
    <div class="w-64 bg-white border border-gray-200 rounded-lg shadow-sm p-4">
        <div class="text-sm text-gray-500 text-center mb-1">Active Jobs</div>
        <div class="flex justify-center items-center">
            <svg data-lucide="briefcase" class="w-6 h-6 text-blue-600"></svg>
            <span class="ml-3 text-2xl font-bold text-gray-800">{{ $activeJobs }}</span>
        </div>
    </div>

    {{-- Pending Review Card --}}
    <div class="w-64 bg-white border border-gray-200 rounded-lg shadow-sm p-4">
        <div class="text-sm text-gray-500 text-center mb-1">Pending Review</div>
        <div class="flex justify-center items-center">
            <svg data-lucide="clock" class="w-6 h-6 text-yellow-500"></svg>
            <span class="ml-3 text-2xl font-bold text-gray-800">{{ $pendingJobs }}</span>
        </div>
    </div>

    {{-- Total Applications Card --}}
    <div class="w-64 bg-white border border-gray-200 rounded-lg shadow-sm p-4">
        <div class="text-sm text-gray-500 text-center mb-1">Total Applications</div>
        <div class="flex justify-center items-center">
            <svg data-lucide="file-text" class="w-6 h-6 text-purple-600"></svg>
            <span class="ml-3 text-2xl font-bold text-gray-800">{{ $totalApplications }}</span>
        </div>
    </div>
</div>



    {{-- Job Postings Card Layout --}}
    <div class="bg-white rounded-xl shadow p-6 max-w-4xl mx-auto border border-gray-200">
        <h3 class="text-xl font-bold text-gray-800 mb-1">Job Postings</h3>
        <p class="text-sm text-gray-500 mb-5">Manage and track all your job postings with approval status</p>

        {{-- Tabs --}}
        <div class="tabs flex gap-2 mb-6">
            <button class="tab-btn px-4 py-2 rounded-full bg-blue-600 text-white font-medium" data-tab="all">All ({{ $jobs['all']->count() }})</button>
            <button class="tab-btn px-4 py-2 rounded-full bg-gray-100 text-gray-700 font-medium" data-tab="active">Active ({{ $jobs['active']->count() }})</button>
            <button class="tab-btn px-4 py-2 rounded-full bg-gray-100 text-gray-700 font-medium" data-tab="pending">Pending ({{ $jobs['pending']->count() }})</button>
            <button class="tab-btn px-4 py-2 rounded-full bg-gray-100 text-gray-700 font-medium" data-tab="rejected">Rejected ({{ $jobs['rejected']->count() }})</button>
        </div>

        {{-- Tab Contents --}}
        @foreach(['all', 'active', 'pending', 'rejected'] as $status)
            <div class="tab-content" id="tab-{{ $status }}" style="{{ $status == 'all' ? '' : 'display: none;' }}">
                @forelse ($jobs[$status] as $job)
                    <div class="flex justify-between items-center border border-gray-200 rounded-lg p-5 mb-4">
                        <div>
                            <div class="text-base font-semibold text-gray-900 flex items-center gap-2">
                                {{ $job->title }}
                                @if ($job->status === 'approved')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 gap-1">
                                        <svg data-lucide="check-circle" class="w-4 h-4"></svg> Approved
                                    </span>
                                @elseif ($job->status === 'pending')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 gap-1">
                                        <svg data-lucide="clock" class="w-4 h-4"></svg> Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 gap-1">
                                        <svg data-lucide="x-circle" class="w-4 h-4"></svg> Rejected
                                    </span>
                                @endif
                            </div>
                            <div class="text-sm text-gray-500 mt-1">
                                Posted: {{ $job->created_at->format('M d, Y') }}
                                @if ($job->status === 'pending')
                                    <div class="text-xs text-yellow-600 mt-1 flex items-center gap-1">
                                        <svg data-lucide="eye" class="w-3.5 h-3.5"></svg>
                                        Your job post is under review by our admin team. You'll be notified once it's approved.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            @if ($job->status === 'approved')
                                <span class="text-xs font-medium px-3 py-1 rounded-full 
                                    {{ $job->applications->count() ? 'bg-indigo-100 text-indigo-800' : 'bg-gray-100 text-gray-400' }}">
                                    {{ $job->applications->count() }} Applications
                                </span>
                            @endif

                            @if ($job->status === 'approved' && $job->applications->count())
                                <a href="{{ route('employer.jobs.applications', $job->id) }}"
                                   class="text-sm font-medium px-4 py-1.5 border border-gray-300 rounded-md hover:bg-gray-100 transition">
                                    View Applications
                                </a>
                            @endif

                            <form action="{{ route('job.destroy', $job->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this job?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-medium px-3 py-1.5 border border-red-300 text-red-600 rounded-md hover:bg-red-50 transition flex items-center gap-1">
                                    <svg data-lucide="trash" class="w-4 h-4"></svg> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">No {{ $status }} jobs found.</p>
                @endforelse
            </div>
        @endforeach
    </div>
</div>
@php
    $type = session('type') ?? 'success';
    $toastClass = match($type) {
        'success' => 'bg-green-100 text-green-800 border border-green-300',
        'delete', 'error' => 'bg-red-100 text-red-700 border border-red-300',
        default => 'bg-gray-100 text-gray-800 border border-gray-300',
    };
@endphp

@if (session('success') || session('error'))
    <div id="flash-toast"
         class="fixed top-6 right-6 z-50 px-5 py-3 rounded-lg shadow-lg text-sm transition-opacity duration-500 ease-in-out {{ $toastClass }}">
        {{ session('success') ?? session('error') }}
    </div>
@endif



{{-- Notifications and Tab Switching Script --}}
<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();

    document.getElementById('notification-btn').addEventListener('click', function () {
        const dropdown = document.getElementById('notification-dropdown');
        dropdown.classList.toggle('hidden');
    });

    // Tab functionality with blue highlight
    document.querySelectorAll('.tab-btn').forEach(button => {
        button.addEventListener('click', () => {
            const tab = button.dataset.tab;

            // Hide all contents
            document.querySelectorAll('.tab-content').forEach(div => div.style.display = 'none');
            document.getElementById(`tab-${tab}`).style.display = 'block';

            // Reset all tab styles
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white');
                btn.classList.add('bg-gray-100', 'text-gray-700');
            });

            // Highlight selected tab
            button.classList.remove('bg-gray-100', 'text-gray-700');
            button.classList.add('bg-blue-600', 'text-white');
        });
    });
    
    document.addEventListener('DOMContentLoaded', function () {
        const toast = document.getElementById('flash-toast');
        if (toast) {
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 500); // Remove from DOM after fade
            }, 3000); // 3 seconds visible
        }
    });


</script>


@endsection
