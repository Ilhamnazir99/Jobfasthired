@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto p-6">
    <h2 class="text-2xl font-bold mb-6">Your Applied Jobs</h2>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-4 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if($applications->isEmpty())
        <p class="text-gray-600">You have not applied for any jobs yet.</p>
    @else
        <div class="space-y-4">
            @foreach($applications as $application)
                <div class="p-4 border rounded shadow-sm">
                    <h3 class="text-lg font-bold">{{ $application->job->title }}</h3>
                    <p class="text-sm text-gray-600">Location: {{ $application->job->location ?? 'Not specified' }}</p>
                    <p class="text-sm text-gray-600">Status: 
                        <span class="font-semibold 
                            @if($application->status == 'Pending') text-yellow-600 
                            @elseif($application->status == 'Approved') text-green-600 
                            @elseif($application->status == 'Rejected') text-red-600 
                            @endif">
                            {{ $application->status }}
                        </span>
                    </p>
                    <div class="mt-3">
                        <a href="{{ route('student.jobs.show', $application->job->id) }}" 
                           class="bg-blue-600 text-white px-4 py-2 rounded">
                            View Job Details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
