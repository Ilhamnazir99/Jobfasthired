<!-- resources/views/student/show_job.blade.php -->
@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-bold mb-4">{{ $job->title }}</h2>
    <p class="text-gray-700 mb-2">Location: {{ $job->location ?? 'Not specified' }}</p>
    <p class="text-gray-700 mb-2">Salary: RM {{ $job->salary ?? 'Not specified' }}</p>
    <p class="text-gray-700 mb-2">Description:</p>
    <p class="text-gray-600 mb-4">{{ $job->description }}</p>

    <a href="{{ route('student.applied.jobs') }}" class="bg-blue-600 text-white px-4 py-2 rounded mt-4 inline-block">
        Back to Applied Jobs
    </a>
</div>
@endsection
