<!-- resources/views/student/partials/job_card.blade.php -->

<div class="p-4 border rounded shadow-sm hover:bg-blue-50 transition cursor-pointer">
    <h3 class="text-lg font-bold">{{ $job->title }}</h3>
    <p class="text-sm text-gray-600">{{ $job->location ?? 'Location not specified' }}</p>
    <p class="text-sm text-gray-600">{{ $job->salary ? 'RM ' . $job->salary : 'Salary not specified' }}</p>
    <p class="mt-2 text-sm text-gray-700">{{ \Illuminate\Support\Str::limit($job->description, 100) }}</p>

    <div class="mt-3 text-right">
        <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Apply Now</a>
    </div>
</div>
