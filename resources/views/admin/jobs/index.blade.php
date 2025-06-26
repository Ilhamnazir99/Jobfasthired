@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Job Management</h2>

    @if (session('success'))
        <div class="mb-4 text-green-700 bg-green-100 border border-green-300 px-4 py-2 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white border border-gray-200 shadow-sm rounded-lg overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-700">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr class="text-xs font-semibold text-gray-600 uppercase">
                    <th class="px-6 py-4">#</th>
                    <th class="px-6 py-4">Title</th>
                    <th class="px-6 py-4">Employer</th>
                    <th class="px-6 py-4">Category</th> {{-- ✅ New column --}}
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Posted At</th>
                    <th class="px-6 py-4">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($jobs as $index => $job)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $index + 1 }}</td>
                        <td class="px-6 py-4">{{ $job->title }}</td>
                        <td class="px-6 py-4">{{ $job->employer->name }}</td>
                        <td class="px-6 py-4">{{ $job->category->name ?? 'N/A' }}</td> {{-- ✅ Show category --}}
                        <td class="px-6 py-4">
                            <span class="inline-block px-2 py-1 text-xs rounded-full font-semibold
                                {{ $job->status === 'approved' ? 'bg-green-100 text-green-700' :
                                   ($job->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ ucfirst($job->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $job->created_at->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 flex flex-wrap gap-2">
                            <button onclick="showJobDetails({{ $job->id }})" class="text-indigo-600 hover:underline text-sm">View</button>
                            @if($job->status !== 'approved')
                                <form method="POST" action="{{ route('admin.jobs.approve', $job->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-blue-600 hover:underline text-sm">Approve</button>
                                </form>
                            @endif
                            @if($job->status !== 'rejected')
                                <form method="POST" action="{{ route('admin.jobs.reject', $job->id) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-yellow-600 hover:underline text-sm">Reject</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.jobs.destroy', $job->id) }}"
                                  onsubmit="return confirm('Delete this job?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center px-6 py-8 text-gray-500">No jobs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- 🔍 Job Details Modal --}}
<div id="job-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto p-6 relative">
        <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-600 hover:text-red-500 text-xl">&times;</button>

        <h3 class="text-2xl font-bold mb-4" id="modal-title">Job Title</h3>
        <div class="text-sm text-gray-700 space-y-2">
            <p><strong>Employer:</strong> <span id="modal-employer"></span></p>
            <p><strong>Status:</strong> <span id="modal-status"></span></p>
            <p><strong>Location:</strong> <span id="modal-location"></span></p>
            <p><strong>Salary:</strong> <span id="modal-salary"></span></p>
            <p><strong>Schedule:</strong> <span id="modal-schedule"></span></p>
            <p><strong>Category:</strong> <span id="modal-category"></span></p> {{-- ✅ --}}
            <p><strong>Address:</strong> <span id="modal-address"></span></p>
            <p><strong>Coordinates:</strong> <span id="modal-coordinates"></span></p>
            <p><strong>Posted:</strong> <span id="modal-created"></span></p>
            <p><strong>Description:</strong></p>
            <div id="modal-description" class="bg-gray-50 p-3 rounded text-gray-800 text-sm whitespace-pre-line"></div>
        </div>
    </div>
</div>

<script>
    const jobs = @json($jobs);

    function showJobDetails(jobId) {
        const job = jobs.find(j => j.id === jobId);
        if (!job) return;

        document.getElementById('modal-title').textContent = job.title;
        document.getElementById('modal-employer').textContent = job.employer?.name || 'N/A';
        document.getElementById('modal-status').textContent = job.status;
        document.getElementById('modal-location').textContent = job.location || 'N/A';
        document.getElementById('modal-salary').textContent = job.salary ? 'RM ' + parseFloat(job.salary).toFixed(2) : 'N/A';
        document.getElementById('modal-schedule').textContent = formatSchedule(job.schedule);
        document.getElementById('modal-category').textContent = job.category?.name || 'N/A'; // ✅ fixed
        document.getElementById('modal-address').textContent = job.address || 'N/A';
        document.getElementById('modal-coordinates').textContent = `${job.latitude ?? '-'}, ${job.longitude ?? '-'}`;
        document.getElementById('modal-created').textContent = new Date(job.created_at).toLocaleDateString();
        document.getElementById('modal-description').textContent = job.description || 'No description provided.';

        document.getElementById('job-modal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('job-modal').classList.add('hidden');
    }

    function formatSchedule(schedule) {
        try {
            const parsed = typeof schedule === 'string' ? JSON.parse(schedule) : schedule;
            return Object.entries(parsed).map(([day, times]) =>
                `${day.charAt(0).toUpperCase() + day.slice(1)}: ${times.start} - ${times.end}`
            ).join(', ');
        } catch {
            return 'Not available';
        }
    }
</script>
@endsection
