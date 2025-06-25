@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
        <i data-lucide="file-text" class="w-6 h-6 text-blue-600"></i> Your Applied Jobs
    </h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-4 shadow">
            {{ session('success') }}
        </div>
    @endif

    @if($applications->isEmpty())
        <div class="text-center text-gray-600">
            <p>You haven't applied for any jobs yet.</p>
            <a href="{{ route('job.search') }}" class="mt-4 inline-block text-blue-600 hover:underline">
                <i data-lucide="search" class="w-4 h-4 inline-block mr-1"></i> Start browsing jobs
            </a>
        </div>
    @else
        <div class="grid gap-6">
            @foreach($applications as $application)
                @php
                    $job = $application->job;
                    $company = $job->company_name ?? $job->employer->company_name ?? 'Company';
                @endphp

                <div class="p-6 bg-white border border-gray-100 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 ease-in-out transform hover:-translate-y-1 opacity-0 animate-fade-in">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">{{ $job->title }}</h3>

                            <p class="text-sm text-gray-500 flex items-center gap-1 mt-1">
                                <i data-lucide="building-2" class="w-4 h-4 text-blue-500"></i> {{ $company }}
                            </p>

                            <p class="text-sm text-gray-500 flex items-center gap-1 mt-1">
                                <i data-lucide="map-pin" class="w-4 h-4 text-red-500"></i> {{ $job->location ?? 'Not specified' }}
                            </p>

                            <p class="text-sm text-gray-500 flex items-center gap-1 mt-1">
                                <i data-lucide="calendar" class="w-4 h-4 text-yellow-600"></i> Applied on {{ $application->created_at->format('d M Y') }}
                            </p>
                        </div>

                        <div>
                            <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full text-center min-w-[90px]
                                @if($application->status == 'Pending') bg-yellow-100 text-yellow-700
                                @elseif($application->status == 'Accepted') bg-green-100 text-green-700
                                @elseif($application->status == 'Rejected') bg-red-100 text-red-700
                                @else bg-gray-200 text-gray-600
                                @endif">
                                {{ $application->status }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <a href="{{ route('student.jobs.show', $job->id) }}" 
                           class="inline-flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                            <i data-lucide="eye" class="w-4 h-4"></i> View Job
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(8px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.5s ease-out forwards;
}
</style>
@endsection
