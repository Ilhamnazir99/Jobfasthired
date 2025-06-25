@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto p-6 bg-white rounded shadow">
        <h2 class="text-2xl font-bold mb-6">Apply for Job: {{ $job->title }}</h2>
        <!-- Job Details -->
        <div class="space-y-3 text-gray-700 mb-5">
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

        <form action="{{ route('student.jobs.submit', $job->id) }}" method="POST" id="application-form">
            @csrf

            {{-- PROFILE SECTION --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                {{-- Left: Profile Summary --}}
                <div class="bg-gray-50 p-4 rounded shadow border">
                    <h3 class="text-xl font-semibold mb-4">Profile Summary</h3>
                    <p class="mb-2"><strong>Name:</strong> <span id="summary-name">{{ Auth::user()->name }}</span></p>
                    <p class="mb-2"><strong>Email:</strong> <span id="summary-email">{{ Auth::user()->email }}</span></p>
                    <p class="mb-2"><strong>Phone:</strong> <span
                            id="summary-phone">{{ Auth::user()->phone_number ?? 'N/A' }}</span></p>
                    <button type="button" onclick="toggleEdit()"
                        class="mt-4 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Edit Profile
                    </button>
                </div>

                {{-- Right: Editable Profile --}}
                <div id="edit-profile" class="hidden bg-white p-4 rounded shadow border">
                    <h3 class="text-xl font-semibold mb-4">Edit Profile</h3>

                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}"
                            class="w-full p-2 border border-gray-300 rounded focus:ring focus:ring-blue-200">
                    </div>

                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                            class="w-full p-2 border border-gray-300 rounded bg-gray-100 cursor-not-allowed" readonly>
                    </div>

                    <div class="mb-4">
                        <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="text" id="phone_number" name="phone_number"
                            value="{{ old('phone_number', Auth::user()->phone_number) }}"
                            class="w-full p-2 border border-gray-300 rounded focus:ring focus:ring-blue-200"
                            placeholder="e.g. 0123456789 or +60123456789">
                    </div>

                    <button type="button" onclick="saveProfile()"
                        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Save Changes
                    </button>
                </div>
            </div>

            {{-- SKILLS SECTION --}}
            <div class="mb-8">
                <h3 class="text-lg font-semibold mb-3">Your Valuable Skills</h3>

                <div id="skills-list" class="flex flex-wrap gap-2 mb-4">
                    @foreach (Auth::user()->skills as $skill)
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm flex items-center">
                            {{ $skill->name }}
                            <button type="button" class="ml-2 text-red-500 hover:text-red-700"
                                onclick="removeSkill({{ $skill->id }}, event)">
                                &times;
                            </button>
                        </span>
                    @endforeach
                </div>

                <div class="flex gap-2">
                    <input type="text" id="new-skill" class="w-full border border-gray-300 rounded px-3 py-2"
                        placeholder="Add a valuable skill (e.g., Time Management, Excel)">
                    <button type="button" onclick="addSkill()"
                        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Add Skill
                    </button>
                </div>
            </div>

            {{-- SUBMIT BUTTON --}}
            <div class="text-right mt-8">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded font-medium">
                    Submit Application
                </button>
            </div>
        </form>
    </div>

    {{-- CSRF Token --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- JavaScript --}}
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
                    console.error('Error:', err);
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
                .catch(error => {
                    console.error('Add Skill Error:', error);
                    alert('Error adding skill');
                });
        }

        function removeSkill(skillId, event) {
            if (!confirm('Are you sure you want to remove this skill?')) return;

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
                .catch(error => {
                    console.error('Remove Skill Error:', error);
                    alert('An error occurred while removing the skill.');
                });
        }
    </script>
@endsection