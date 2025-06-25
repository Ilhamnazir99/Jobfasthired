@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-8 px-4">

    {{-- Header Row: Welcome + Action Buttons --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <h2 class="text-2xl font-semibold text-gray-800">Welcome, {{ auth()->user()->name }}!</h2>

        <div class="flex items-center gap-4">
            {{-- Post Job Button --}}
            <a href="{{ route('job.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded flex items-center gap-2 shadow-sm">
                <svg data-lucide="plus-circle" class="w-5 h-5"></svg> Post a New Job
            </a>
          
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="flex justify-center flex-wrap gap-6 mb-10">
        @php
            $cards = [
                ['label' => 'Active Jobs', 'value' => $activeJobs, 'icon' => 'briefcase', 'color' => 'text-blue-600'],
                ['label' => 'Pending Review', 'value' => $pendingJobs, 'icon' => 'clock', 'color' => 'text-yellow-500'],
                ['label' => 'Total Applications', 'value' => $totalApplications, 'icon' => 'file-text', 'color' => 'text-purple-600'],
            ];
        @endphp

        @foreach($cards as $card)
            <div class="w-64 bg-white border border-gray-200 rounded-lg shadow-sm p-5 text-center">
                <div class="text-sm text-gray-500 mb-1">{{ $card['label'] }}</div>
                <div class="flex justify-center items-center">
                    <svg data-lucide="{{ $card['icon'] }}" class="w-6 h-6 {{ $card['color'] }}"></svg>
                    <span class="ml-3 text-2xl font-bold text-gray-800">{{ $card['value'] }}</span>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Job Postings --}}
    <div class="bg-white rounded-xl shadow p-6 max-w-5xl mx-auto border border-gray-200">
        <h3 class="text-xl font-bold text-gray-800 mb-1">Job Postings</h3>
        <p class="text-sm text-gray-500 mb-5">Manage and track all your job postings with approval status</p>

        {{-- Tabs --}}
        <div class="tabs flex flex-wrap gap-3 mb-6">
            @foreach (['all' => 'All', 'active' => 'Active', 'pending' => 'Pending', 'rejected' => 'Rejected'] as $key => $label)
                <button class="tab-btn px-4 py-2 rounded-full font-medium {{ $key === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700' }}"
                        data-tab="{{ $key }}">{{ $label }} ({{ $jobs[$key]->count() }})</button>
            @endforeach
        </div>

        {{-- Job Listings --}}
        @foreach(['all', 'active', 'pending', 'rejected'] as $status)
            <div class="tab-content" id="tab-{{ $status }}" style="{{ $status == 'all' ? '' : 'display: none;' }}">
                @forelse ($jobs[$status] as $job)
                    <div class="flex justify-between items-start border border-gray-200 rounded-lg p-5 mb-4">
                        <div>
                            <div class="text-base font-semibold text-gray-900 flex items-center gap-2">
                                {{ $job->title }}
                                @if ($job->status === 'approved')
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-green-100 text-green-800 rounded-full gap-1">
                                        <svg data-lucide="check-circle" class="w-4 h-4"></svg> Approved
                                    </span>
                                @elseif ($job->status === 'pending')
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full gap-1">
                                        <svg data-lucide="clock" class="w-4 h-4"></svg> Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 text-xs font-medium bg-red-100 text-red-700 rounded-full gap-1">
                                        <svg data-lucide="x-circle" class="w-4 h-4"></svg> Rejected
                                    </span>
                                @endif
                            </div>
                            <div class="text-sm text-gray-500 mt-1">Posted: {{ $job->created_at->format('M d, Y') }}</div>
                            @if ($job->status === 'pending')
                                <p class="text-xs text-yellow-600 mt-1 flex items-center gap-1">
                                    <svg data-lucide="eye" class="w-3.5 h-3.5"></svg> Awaiting admin review
                                </p>
                            @endif
                        </div>

                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
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

                            <form action="{{ route('job.destroy', $job->id) }}" method="POST"
                                  onsubmit="return confirm('Are you sure you want to delete this job?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm px-3 py-1.5 border border-red-300 text-red-600 rounded-md hover:bg-red-50 flex items-center gap-1">
                                    <svg data-lucide="trash" class="w-4 h-4"></svg> Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No {{ $status }} jobs found.</p>
                @endforelse
            </div>
        @endforeach
    </div>
</div>

{{-- Flash Toast --}}
@if (session('success') || session('error'))
    @php
        $toastClass = session('success')
            ? 'bg-green-100 text-green-800 border border-green-300'
            : 'bg-red-100 text-red-700 border border-red-300';
    @endphp
    <div id="flash-toast" class="fixed top-6 right-6 z-50 px-5 py-3 rounded-lg shadow-lg text-sm transition-opacity duration-500 ease-in-out {{ $toastClass }}">
        {{ session('success') ?? session('error') }}
    </div>
@endif

{{-- Scripts --}}
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();

   
    // Tabs
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const tab = btn.dataset.tab;

            document.querySelectorAll('.tab-content').forEach(div => div.style.display = 'none');
            document.getElementById(`tab-${tab}`).style.display = 'block';

            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('bg-blue-600', 'text-white'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.add('bg-gray-100', 'text-gray-700'));

            btn.classList.remove('bg-gray-100', 'text-gray-700');
            btn.classList.add('bg-blue-600', 'text-white');
        });
    });

    // Auto-hide toast
    document.addEventListener('DOMContentLoaded', () => {
        const toast = document.getElementById('flash-toast');
        if (toast) {
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        }
    });
</script>
@endsection
