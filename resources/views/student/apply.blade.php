@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-bold mb-6">Apply for Job: {{ $job->title }}</h2>
    <p class="text-gray-700 mb-6">Location: {{ $job->location }}</p>

    <form action="{{ route('student.jobs.submit', $job->id) }}" method="POST" id="application-form">
        @csrf

        {{-- PROFILE SECTION --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            {{-- Left: Profile Summary Card --}}
            <div class="border p-4 rounded shadow bg-gray-50">
                <h3 class="text-xl font-semibold mb-4">Profile Summary</h3>
                <p><strong>Name:</strong> <span id="summary-name">{{ Auth::user()->name }}</span></p>
                <p><strong>Email:</strong> <span id="summary-email">{{ Auth::user()->email }}</span></p>
                <button type="button" onclick="toggleEdit()" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded">
                    Edit Profile
                </button>
            </div>

            {{-- Right: Editable Profile Form --}}
            <div id="edit-profile" class="hidden border p-4 rounded shadow bg-white">
                <h3 class="text-xl font-semibold mb-4">Edit Profile</h3>

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}"
                        class="w-full p-2 border border-gray-300 rounded">
                </div>

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                        class="w-full p-2 border border-gray-300 rounded">
                </div>

                <button type="button" onclick="saveProfile()" class="bg-green-600 text-blue px-4 py-2 rounded">
                    Save Changes
                </button>
            </div>
        </div>

        {{-- SKILLS SECTION --}}
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-2">Your Skills</h3>

            <div class="flex flex-wrap gap-2 mb-4" id="skills-list">
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
                    placeholder="Enter skill (e.g., Laravel)">
                <button type="button" onclick="addSkill()" class="bg-green-600 text-white px-4 py-2 rounded">
                    Add Skill
                </button>
            </div>
        </div>

        {{-- SUBMIT BUTTON --}}
        <div class="text-right mt-6">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
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
        const form = document.getElementById('edit-profile');
        form.classList.toggle('hidden');
    }

    function saveProfile() {
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();

    fetch('{{ route('student.profile.update') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ name: name, email: email })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('summary-name').textContent = name;
            document.getElementById('summary-email').textContent = email;
            toggleEdit();
        } else {
            alert('Failed to update profile');
        }
    })
    .catch(error => {
        console.error('Update Profile Error:', error);
        alert('An error occurred while updating your profile.');
    });
}


    function addSkill() {
        const skill = document.getElementById('new-skill').value.trim();
        if (!skill) return;

        fetch('{{ route('profile.skills.store') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ name: skill })
        })
        .then(response => response.json())
        .then(data => location.reload())
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
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
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
            console.error('Error:', error);
            alert('An error occurred while removing the skill.');
        });
    }
</script>
@endsection
