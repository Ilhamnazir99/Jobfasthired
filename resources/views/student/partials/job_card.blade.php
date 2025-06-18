<!-- resources/views/student/partials/job_card.blade.php -->

<div class="p-4 border rounded shadow-sm job-card cursor-pointer hover:bg-blue-50 transition"
     data-job-id="{{ $job->id }}"
     onclick="showJobOnMap({{ $job->id }})">

    <h3 class="text-lg font-bold">{{ $job->title }}</h3>

    @if ($job->company_name ?? $job->employer->company_name)
        <p class="text-sm text-gray-500">
            {{ $job->company_name ?? $job->employer->company_name }}
        </p>
    @endif

    <p class="text-sm text-gray-600">{{ $job->location ?? 'Location not specified' }}</p>

    @if ($job->salary)
        <p class="text-sm text-gray-600">RM {{ number_format($job->salary, 2) }} per hour</p>
    @endif

    <p class="mt-2 text-sm text-gray-700">{{ \Illuminate\Support\Str::limit($job->description, 100) }}</p>

    {{-- SCHEDULE --}}
    @php
        $scheduleData = is_array($job->schedule) ? $job->schedule : json_decode($job->schedule, true);
    @endphp

    @if ($scheduleData)
        <div class="flex items-start text-sm text-gray-600 mt-1">
            <svg data-lucide="calendar-clock" class="w-4 h-4 mr-1 mt-1 stroke-blue-500"></svg>
            <div class="space-y-1">
                @foreach ($scheduleData as $day => $times)
                    <div><strong>{{ ucfirst($day) }}:</strong> {{ $times['start'] }} - {{ $times['end'] }}</div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- WEEKLY PAY --}}
    @if ($job->weekly_pay)
        <div class="flex items-center text-sm text-gray-600 mt-1">
            <svg data-lucide="wallet" class="w-4 h-4 mr-1 stroke-yellow-600"></svg>
            <span>Est. Weekly Pay: RM {{ $job->weekly_pay }}</span>
        </div>
    @endif

    <div class="mt-3 text-right">
        <a href="/jobs/{{ $job->id }}/apply" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Apply Now
        </a>
    </div>
</div>
