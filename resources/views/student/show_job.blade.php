@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto p-6 bg-white rounded-xl shadow-md mt-10">
        <!-- Job Title -->
        <h2 class="text-3xl font-extrabold text-gray-800 mb-4 flex items-center">
            <svg data-lucide="briefcase" class="w-6 h-6 mr-2 text-blue-600"></svg>
            {{ $job->title }}
        </h2>

        <!-- Job Details -->
        <div class="space-y-3 text-gray-700">
            <!-- Location -->
            <div class="flex items-start">
                <svg data-lucide="map-pin" class="w-5 h-5 text-gray-500 mt-1 mr-2"></svg>
                <p><strong>Location:</strong> {{ $job->location ?? 'Not specified' }}</p>
            </div>

            <!-- Salary -->
            <div class="flex items-start">
                <svg data-lucide="dollar-sign" class="w-5 h-5 text-green-600 mt-1 mr-2"></svg>
                <p><strong>Salary:</strong> RM {{ number_format($job->salary ?? 0, 2) }}</p>
            </div>

            @if ($job->weekly_pay)
                <div class="flex items-center text-gray-700 mb-2">
                    <svg data-lucide="wallet" class="w-5 h-5 text-yellow-600 mr-2"></svg>
                    <span><strong>Estimated Weekly Pay:</strong> RM {{ $job->weekly_pay }}</span>
                </div>
            @endif


            <!-- Schedule -->
            @php
                $schedule = is_array($job->schedule) ? $job->schedule : json_decode($job->schedule, true);
            @endphp
            @if ($schedule)
                <div class="flex items-start">
                    <svg data-lucide="calendar-clock" class="w-5 h-5 text-blue-500 mt-1 mr-2"></svg>
                    <div>
                        <strong>Schedule:</strong>
                        <ul class="list-disc list-inside mt-1">
                            @foreach ($schedule as $day => $times)
                                <li>{{ ucfirst($day) }}: {{ $times['start'] }} - {{ $times['end'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Skills -->
            @if ($job->skills && $job->skills->count())
                <div class="flex items-start">
                    <svg data-lucide="star" class="w-5 h-5 text-purple-500 mt-1 mr-2"></svg>
                    <div>
                        <strong>Required Skills:</strong>
                        <div class="flex flex-wrap gap-2 mt-1">
                            @foreach ($job->skills as $skill)
                                <span
                                    class="bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded">{{ $skill->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Description -->
            <div class="flex items-start">
                <svg data-lucide="file-text" class="w-5 h-5 text-gray-500 mt-1 mr-2"></svg>
                <div>
                    <strong>Description:</strong>
                    <p class="mt-1 text-gray-600 leading-relaxed">{{ $job->description }}</p>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="mt-6">
            <a href="{{ route('student.applied.jobs') }}"
                class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded transition">
                <svg data-lucide="arrow-left" class="w-4 h-4 mr-2"></svg>
                Back to Applied Jobs
            </a>
        </div>
    </div>
@endsection