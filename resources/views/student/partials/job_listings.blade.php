@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-5 p-4 bg-white shadow-md rounded">
    <h2 class="text-2xl font-bold mb-4">Available Part-Time Jobs</h2>

    @if ($jobs->count() > 0)
        <ul class="space-y-4">
            @foreach ($jobs as $job)
                <li class="p-4 border rounded bg-gray-50">
                    <h3 class="text-lg font-semibold">{{ $job->title }}</h3>
                    <p class="text-sm text-gray-600">{{ $job->description }}</p>
                    <p class="text-sm text-gray-500 mt-1">Posted on {{ $job->created_at->format('d M Y') }}</p>
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-gray-600">No job listings available at the moment.</p>
    @endif
</div>
@endsection
