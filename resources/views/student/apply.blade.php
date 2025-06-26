@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-6 py-8">
        <!-- 🔙 Back Button -->
    <a href="{{ route('job.search') }}"
       class="inline-flex items-center mb-6 text-sm text-blue-600 hover:underline hover:text-blue-800 font-medium">
        <svg data-lucide="arrow-left" class="w-4 h-4 mr-1 stroke-blue-600"></svg>
        Back to Job Search
    </a>



    <h2 class="text-3xl font-bold text-gray-800 mb-6">📄 Apply for: <span class="text-blue-700">{{ $job->title }}</span></h2>

    <!-- 📍 Job Meta (Enhanced) -->
<div class="mb-8 p-6 bg-blue-50 border-l-4 border-blue-400 rounded shadow-sm text-gray-700">
    <div class="flex items-center mb-2 text-sm">
        <svg data-lucide="map-pin" class="w-5 h-5 mr-2 stroke-blue-500"></svg>
        <strong class="mr-1">Location:</strong> {{ $job->location ?? 'Not specified' }}
    </div>

    <div class="flex items-center mb-2 text-sm">
        <svg data-lucide="tag" class="w-5 h-5 mr-2 stroke-purple-600"></svg>
        <strong class="mr-1">Category:</strong> {{ $job->category->name ?? 'N/A' }}
    </div>

    <div class="flex items-center mb-2 text-sm">
        <svg data-lucide="building" class="w-5 h-5 mr-2 stroke-gray-700"></svg>
        <strong class="mr-1">Company:</strong> {{ optional($job->employer)->company_name ?? 'N/A' }}
    </div>

    <div class="flex items-center mb-2 text-sm">
        <svg data-lucide="dollar-sign" class="w-5 h-5 mr-2 stroke-green-600"></svg>
        <strong class="mr-1">Hourly Pay:</strong> RM {{ number_format($job->salary, 2) }}
    </div>

    @php
        $scheduleData = is_array($job->schedule) ? $job->schedule : json_decode($job->schedule, true) ?? [];
        $totalHours = 0;
        foreach ($scheduleData as $day => $times) {
            if (isset($times['start'], $times['end'])) {
                try {
                    $start = \Carbon\Carbon::createFromFormat('H:i', $times['start']);
                    $end = \Carbon\Carbon::createFromFormat('H:i', $times['end']);
                    $totalHours += $end->diffInMinutes($start) / 60;
                } catch (Exception $e) {}
            }
        }
    @endphp

    <div class="flex items-center mb-2 text-sm">
        <svg data-lucide="wallet" class="w-5 h-5 mr-2 stroke-yellow-600"></svg>
        <strong class="mr-1">Estimated Weekly Pay:</strong> RM {{ number_format($job->salary * $totalHours, 2) }}
    </div>

    @if (!empty($scheduleData))
        <div class="flex items-start mt-2 text-sm">
            <svg data-lucide="calendar-clock" class="w-5 h-5 mr-2 mt-1 stroke-blue-500"></svg>
            <div>
                <strong class="block mb-1">Schedule:</strong>
                <div class="grid grid-cols-2 gap-x-6 gap-y-1">
                    @foreach ($scheduleData as $day => $times)
                        @if (!empty($times['start']) && !empty($times['end']))
                            @php
                                $start = \Carbon\Carbon::createFromFormat('H:i', $times['start'])->format('g:i A');
                                $end = \Carbon\Carbon::createFromFormat('H:i', $times['end'])->format('g:i A');
                            @endphp
                            <div><strong>{{ ucfirst($day) }}:</strong> {{ $start }} - {{ $end }}</div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>


    <form action="{{ route('student.jobs.submit', $job->id) }}" method="POST" id="application-form">
        @csrf

        

   <!-- 👤 Profile Section -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    <!-- ✅ Summary -->
    <div class="bg-white border rounded-lg p-5 shadow">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Profile Summary</h3>

        <p class="mb-2"><strong>Name:</strong> <span id="summary-name">{{ Auth::user()->name }}</span></p>
        <p class="mb-2"><strong>Email:</strong> <span id="summary-email">{{ Auth::user()->email }}</span></p>
        <p class="mb-2"><strong>Phone:</strong> <span id="summary-phone">{{ Auth::user()->phone_number ?? 'N/A' }}</span></p>

        <div class="flex justify-end mt-4">
            <button type="button" onclick="toggleEdit()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow flex items-center gap-2">
                <svg data-lucide="pencil" class="w-4 h-4 stroke-white"></svg> Edit Profile
            </button>
        </div>
    </div>

    <!-- ✅ Edit -->
    <div id="edit-profile" class="hidden bg-white border rounded-lg p-5 shadow">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Edit Profile</h3>

        <label class="block mb-3">
            <span class="text-sm text-gray-600">Name</span>
            <input type="text" name="name" id="name" value="{{ old('name', Auth::user()->name) }}"
                   class="w-full mt-1 border rounded px-3 py-2 focus:ring focus:ring-blue-200">
        </label>

        <label class="block mb-3">
            <span class="text-sm text-gray-600">Email</span>
            <input type="email" name="email" id="email" readonly
                   value="{{ old('email', Auth::user()->email) }}"
                   class="w-full mt-1 border rounded px-3 py-2 bg-gray-100 text-gray-500 cursor-not-allowed">
        </label>

        <label class="block mb-4">
            <span class="text-sm text-gray-600">Phone Number</span>
            <input type="text" name="phone_number" id="phone_number"
                   value="{{ old('phone_number', Auth::user()->phone_number) }}"
                   placeholder="e.g. 0123456789"
                   class="w-full mt-1 border rounded px-3 py-2 focus:ring focus:ring-blue-200">
        </label>

        <div class="flex justify-end">
            <button type="button" onclick="saveProfile()"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow flex items-center gap-2">
                <svg data-lucide="save" class="w-4 h-4 stroke-white"></svg> Save Changes
            </button>
        </div>
    </div>
</div>

<!-- 🧠 Skills (Icon Removed) -->
<div class="bg-white border rounded-lg p-5 shadow mb-8">
    <h3 class="text-lg font-semibold text-gray-800 mb-3">Your Valuable Skills</h3>

    <div id="skills-list" class="flex flex-wrap gap-2 mb-4">
        @foreach (Auth::user()->skills as $skill)
            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm flex items-center">
                {{ $skill->name }}
                <button type="button" onclick="removeSkill({{ $skill->id }}, event)"
                        class="ml-2 text-red-500 hover:text-red-700 font-bold">
                    &times;
                </button>
            </span>
        @endforeach
    </div>

    <div class="flex gap-2 justify-end">
        <input type="text" id="new-skill"
               class="flex-1 border border-gray-300 rounded px-3 py-2"
               placeholder="Add a skill (e.g. Excel, Communication)">
        <button type="button" onclick="addSkill()"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded flex items-center gap-2">
            <svg data-lucide="plus" class="w-4 h-4 stroke-white"></svg> Add Skill
        </button>
    </div>
</div>

<!-- ✅ Submit -->
<div class="flex justify-end">
    <button type="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded font-semibold shadow flex items-center gap-2">
        <svg data-lucide="send" class="w-5 h-5 stroke-white"></svg> Submit Application
    </button>
</div>

    </form>
</div>

{{-- Scripts --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    function toggleEdit() {
        document.getElementById('edit-profile').classList.toggle('hidden');
    }

    function saveProfile() {
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const phone_number = document.getElementById('phone_number').value.trim();

        fetch('{{ route('student.profile.update') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name, email, phone_number })
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                document.getElementById('summary-name').textContent = name;
                document.getElementById('summary-email').textContent = email;
                document.getElementById('summary-phone').textContent = phone_number || 'N/A';
                toggleEdit();
            } else {
                alert('Failed to update profile');
            }
        }).catch(err => {
            alert('Something went wrong updating profile.');
        });
    }

    function addSkill() {
        const skill = document.getElementById('new-skill').value.trim();
        if (!skill) return;

        fetch('{{ route('profile.skills.store') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name: skill })
        })
        .then(response => response.json())
        .then(() => location.reload())
        .catch(() => alert('Error adding skill'));
    }

    function removeSkill(skillId, event) {
        if (!confirm('Remove this skill?')) return;

        fetch(`/profile/skills/${skillId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                event.target.closest('span').remove();
            } else {
                alert('Failed to remove skill.');
            }
        })
        .catch(() => alert('Error removing skill.'));
    }
</script>
@endsection
