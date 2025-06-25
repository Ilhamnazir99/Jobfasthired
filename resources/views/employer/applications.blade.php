@extends('layouts.app')

@section('content')
<div class="container mx-auto mt-6 p-6 bg-white rounded shadow">

    <a href="{{ route('employer.dashboard') }}" class="text-blue-600 hover:underline text-sm mb-4 inline-block">
        ← Back to Dashboard
    </a>

    <h2 class="text-2xl font-bold text-gray-800 mb-4">
        {{ $job->title }} 
        <span class="text-sm text-gray-600 ml-2">— {{ $job->applications->count() }} Applications</span>
    </h2>

    @php
        $pendingCount = $job->applications->where('status', 'Pending')->count();
        $decidedCount = $job->applications->whereIn('status', ['Accepted', 'Rejected'])->count();
    @endphp

    {{-- Tabs --}}
    <div class="flex gap-3 mb-6">
        <button class="tab-btn bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded text-sm font-medium flex items-center gap-2" data-tab="pending">
            <svg data-lucide="clock" class="w-4 h-4"></svg> Pending ({{ $pendingCount }})
        </button>
        <button class="tab-btn bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded text-sm font-medium flex items-center gap-2" data-tab="decided">
            <svg data-lucide="badge-check" class="w-4 h-4"></svg> Decision Made ({{ $decidedCount }})
        </button>
    </div>

    {{-- Pending Applications --}}
    <div id="tab-pending" class="tab-content">
        @forelse ($job->applications->where('status', 'Pending') as $application)
            @php
                $student = $application->student;
                $collapseId = 'collapse-' . $application->id;
                $arrowId = 'arrow-' . $application->id;
            @endphp

            <div class="border rounded-lg p-4 mb-4 bg-gray-50">
                <div class="flex justify-between items-center cursor-pointer" onclick="toggleDetails('{{ $collapseId }}', '{{ $arrowId }}')">
                    <div>
                        <div class="flex items-center gap-3">
                         @if($student->profile_image)
                            <img src="{{ asset('images/' . $student->profile_image) }}" alt="Profile Image" class="w-10 h-10 rounded-full object-cover border">
                        @else
                            <img src="{{ asset('images/default-avatar.png') }}" alt="Default Avatar" class="w-10 h-10 rounded-full object-cover border">
                        @endif

                            <p class="font-semibold text-lg">{{ $student->name }}</p>
                        </div>
                        <p class="text-sm text-gray-500 mt-1 italic">Applied: {{ $application->created_at->format('M d, Y') }}</p>
                    </div>

                    <div class="flex items-center gap-2 text-gray-500">
                        <span class="text-sm">{{ ucfirst($application->status) }}</span>
                        <svg id="{{ $arrowId }}" data-lucide="chevron-down" class="w-4 h-4 transition-transform"></svg>
                    </div>
                </div>

                <div id="{{ $collapseId }}" class="mt-4 hidden border-t pt-4">
                    <div class="flex justify-between">
                        <div>
                            <div class="font-semibold mb-1 flex items-center gap-1">
                                <svg data-lucide="mail" class="w-4 h-4"></svg> {{ $student->email }}
                            </div>
                            <div class="text-sm text-gray-600 flex items-center gap-1">
                                <svg data-lucide="phone" class="w-4 h-4"></svg> {{ $student->phone_number ?? 'No phone' }}
                            </div>
                        </div>
                        <div>
                            <div class="font-semibold mb-1">Skills</div>
                            <div class="text-sm text-gray-700">{{ implode(', ', $student->skills->pluck('name')->toArray()) ?: 'No skills listed' }}</div>
                        </div>
                    </div>

                   <div class="flex justify-end mt-4 gap-2 pt-3 border-t">
                    <form method="POST" action="{{ route('employer.updateApplication', $application->id) }}">
                        @csrf
                        @method('PATCH')
                        <div class="flex gap-2"> <!-- Flex container for buttons -->
                          <button name="status" value="Rejected" class="bg-rose-500 hover:bg-rose-600 text-white px-4 py-1 rounded text-sm flex items-center gap-1 transition">
                            <svg data-lucide="x" class="w-4 h-4"></svg> Reject
                        </button>

                          <button name="status" value="Accepted" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-1 rounded text-sm flex items-center gap-1 transition">
                            <svg data-lucide="check" class="w-4 h-4"></svg> Accept
                        </button>

                        </div>
                    </form>
                </div>

                </div>
            </div>
        @empty
            <p class="text-gray-500">No pending applications.</p>
        @endforelse
    </div>

    {{-- Decision Made Applications --}}
    <div id="tab-decided" class="tab-content hidden">
        {{-- Filter buttons --}}
        <div class="flex gap-2 mb-4">
           <button onclick="filterDecision('all')" class="filter-btn bg-gray-200 hover:bg-blue-100 text-sm px-3 py-1 rounded">All</button>
           <button onclick="filterDecision('Accepted')" class="filter-btn bg-green-100 hover:bg-green-200 text-green-800 text-sm px-3 py-1 rounded">Accepted</button>
           <button onclick="filterDecision('Rejected')" class="filter-btn bg-red-100 hover:bg-red-200 text-red-800 text-sm px-3 py-1 rounded">Rejected</button>

        </div>

        @forelse ($job->applications->whereIn('status', ['Accepted', 'Rejected']) as $application)
            @php
                $student = $application->student;
                $collapseId = 'collapse-decided-' . $application->id;
                $arrowId = 'arrow-decided-' . $application->id;
            @endphp

            <div class="border rounded-lg p-4 mb-4 bg-gray-50 decision-card" data-status="{{ $application->status }}">
                <div class="flex justify-between items-center cursor-pointer" onclick="toggleDetails('{{ $collapseId }}', '{{ $arrowId }}')">
                    <div>
                        <div class="flex items-center gap-3">
                        @if($student->profile_image)
                            <img src="{{ asset('images/' . $student->profile_image) }}" alt="Profile Image" class="w-10 h-10 rounded-full object-cover border">
                        @else
                            <img src="{{ asset('images/default-avatar.png') }}" alt="Default Avatar" class="w-10 h-10 rounded-full object-cover border">
                        @endif

                            <p class="font-semibold text-lg">{{ $student->name }}</p>
                        </div>
                        <p class="text-sm text-gray-500 mt-1 italic">Applied: {{ $application->created_at->format('M d, Y') }}</p>
                    </div>

                    <div class="flex items-center gap-2 text-gray-500">
                        <span class="text-sm">{{ ucfirst($application->status) }}</span>
                        <svg id="{{ $arrowId }}" data-lucide="chevron-down" class="w-4 h-4 transition-transform"></svg>
                    </div>
                </div>

                <div id="{{ $collapseId }}" class="mt-4 hidden border-t pt-4">
                    <div class="flex justify-between">
                        <div>
                            <div class="font-semibold mb-1 flex items-center gap-1">
                                <svg data-lucide="mail" class="w-4 h-4"></svg> {{ $student->email }}
                            </div>
                            <div class="text-sm text-gray-600 flex items-center gap-1">
                                <svg data-lucide="phone" class="w-4 h-4"></svg> {{ $student->phone_number ?? 'No phone' }}
                            </div>
                        </div>
                        <div>
                            <div class="font-semibold mb-1">Skills</div>
                            <div class="text-sm text-gray-700">{{ implode(', ', $student->skills->pluck('name')->toArray()) ?: 'No skills listed' }}</div>
                        </div>
                    </div>

                    <div class="mt-4 text-sm font-semibold border-t pt-3">
                        <span class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-full flex items-center gap-1">
                            <svg data-lucide="check-circle" class="w-4 h-4"></svg>
                            Decision Made — {{ ucfirst($application->status) }}
                        </span>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-500">No reviewed applications yet.</p>
        @endforelse
    </div>
</div>

{{-- Scripts --}}
<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();

    function toggleDetails(id, arrowId) {
        const el = document.getElementById(id);
        const arrow = document.getElementById(arrowId);
        el.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');
    }

    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const selectedTab = btn.dataset.tab;
            document.querySelectorAll('.tab-content').forEach(div => div.classList.add('hidden'));
            document.getElementById(`tab-${selectedTab}`).classList.remove('hidden');

            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('bg-blue-600', 'text-white');
                b.classList.add('bg-gray-100', 'text-gray-800');
            });

            btn.classList.remove('bg-gray-100', 'text-gray-800');
            btn.classList.add('bg-blue-600', 'text-white');
        });
    });

   function filterDecision(status) {
    const cards = document.querySelectorAll('.decision-card');
    cards.forEach(card => {
        card.style.display = (status === 'all' || card.dataset.status === status) ? 'block' : 'none';
    });

    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove(
            'bg-blue-600', 'bg-green-600', 'bg-red-600',
            'text-white', 'text-green-800', 'text-red-800', 'text-blue-800',
            'bg-green-100', 'bg-red-100', 'bg-blue-100'
        );
        btn.classList.add('bg-gray-200', 'text-gray-800');
    });

    event.target.classList.remove('bg-gray-200', 'text-gray-800');

    if (status === 'Accepted') {
        event.target.classList.add('bg-green-600', 'text-white');
    } else if (status === 'Rejected') {
        event.target.classList.add('bg-red-600', 'text-white');
    } else {
        event.target.classList.add('bg-blue-600', 'text-white');
    }
}


</script>

@endsection
