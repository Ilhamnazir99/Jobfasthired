@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Job Management</h2>

    {{-- Success & Error Messages --}}
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
                        <td class="px-6 py-4">
                            <span class="inline-block px-2 py-1 text-xs rounded-full font-semibold
                                {{ $job->status === 'approved' ? 'bg-green-100 text-green-700' :
                                   ($job->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ ucfirst($job->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $job->created_at->format('Y-m-d') }}</td>
                        <td class="px-6 py-4 flex space-x-2">
                            @if($job->status !== 'approved')
                                <form method="POST" action="{{ route('admin.jobs.approve', $job->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-blue-600 hover:underline text-sm">Approve</button>
                                </form>
                            @endif

                            @if($job->status !== 'rejected')
                                <form method="POST" action="{{ route('admin.jobs.reject', $job->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-yellow-600 hover:underline text-sm">Reject</button>
                                </form>
                            @endif

                            <form method="POST" action="{{ route('admin.jobs.destroy', $job->id) }}"
                                  onsubmit="return confirm('Delete this job?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center px-6 py-8 text-gray-500">No jobs found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
