@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <h2 class="text-3xl font-bold text-gray-800 mb-8">My Profile</h2>

    {{-- Profile Overview Header --}}
    @php $user = Auth::user(); @endphp

    <div class="mb-8 p-8 bg-white shadow rounded-lg text-center dark:bg-white dark:text-black">
        <div class="flex flex-col items-center justify-center">
            <div class="relative">
                @if ($user->profile_image)
                    <img src="{{ asset('images/' . $user->profile_image) }}" alt="Profile Image" class="w-32 h-32 rounded-full border-4 border-white shadow object-cover">
                @else
                    <img src="{{ asset('default-avatar.png') }}" alt="Default Avatar" class="w-32 h-32 rounded-full border-4 border-white shadow object-cover">
                @endif

                <!-- Upload icon -->
                <label for="profile_image" class="absolute bottom-0 right-0 bg-indigo-600 text-white p-1.5 rounded-full cursor-pointer shadow hover:bg-indigo-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553 2.276A1 1 0 0120 13.118v2.764a1 1 0 01-.447.842L15 19.118M9 10L4.447 12.276A1 1 0 004 13.118v2.764a1 1 0 00.447.842L9 19.118" />
                    </svg>
                    <input id="profile_image" type="file" name="profile_image" class="hidden" onchange="this.form.submit()" form="profile-image-form">
                </label>
            </div>

            <h2 class="mt-4 text-2xl font-semibold text-gray-900">{{ $user->name }}</h2>

            @if($user->role === 'student')
                <div class="mt-3 flex flex-wrap justify-center gap-2">
                    @foreach($user->skills as $skill)
                        <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-medium">
                            {{ $skill->name }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Hidden form for image update --}}
    <form id="profile-image-form" method="POST" action="{{ route('profile.image.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
    </form>

    {{-- Profile Information --}}
    <div class="mb-6 p-6 bg-white shadow rounded-lg">
        @include('profile.partials.update-profile-information-form')
    </div>

    {{-- Password --}}
    <div class="mb-6 p-6 bg-white shadow rounded-lg">
        @include('profile.partials.update-password-form')
    </div>

    {{-- Skills Section (Only for students) --}}
    @if($user->role === 'student')
        <div class="mb-6 p-6 bg-white shadow rounded-lg">
            <div>
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Add Skills</h2>

                {{-- Skills List --}}
                <div id="skills-list" class="flex flex-wrap gap-2 mb-4">
                    @foreach($user->skills as $skill)
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm flex items-center">
                            {{ $skill->name }}
                            <button
                                type="button"
                                class="ml-2 text-red-500 hover:text-red-700"
                                onclick="removeSkill({{ $skill->id }}, event)"
                            >
                                &times;
                            </button>
                        </span>
                    @endforeach
                </div>

                {{-- Add Skill Form --}}
                <form id="add-skill-form" method="POST" action="{{ route('profile.skills.store') }}" class="flex gap-2">
                    @csrf
                    <input
                        type="text"
                        id="new-skill"
                        name="name"
                        placeholder="Enter skill (e.g., Laravel)"
                        class="w-full border border-gray-300 rounded px-3 py-2 text-gray-900"
                    >
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded">
                        Add Skill
                    </button>
                </form>
            </div>
        </div>
    @endif

    {{-- Delete Account --}}
    <div class="p-6 bg-white shadow rounded-lg">
        @include('profile.partials.delete-user-form')
    </div>
</div>

{{-- Skill Removal Script --}}
<script>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('add-skill-form');
        if (!form) return;

        const skillInput = document.getElementById('new-skill');
        const submitBtn = form.querySelector('button[type="submit"]');

        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const skillName = skillInput.value.trim();
            if (!skillName) return;

            submitBtn.disabled = true;
            const originalBtnContent = submitBtn.innerHTML;
            submitBtn.innerHTML = `<svg class="animate-spin h-5 w-5 text-white mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>`;

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ name: skillName })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const skillsList = document.getElementById('skills-list');
                    const span = document.createElement('span');
                    span.className = 'bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm flex items-center opacity-0 transition-opacity duration-300';
                    span.innerHTML = `
                        ${data.skill.name}
                        <button
                            type="button"
                            class="ml-2 text-red-500 hover:text-red-700"
                            onclick="removeSkill(${data.skill.id}, event)"
                        >
                            &times;
                        </button>
                    `;
                    skillsList.appendChild(span);
                    setTimeout(() => {
                        span.classList.remove('opacity-0');
                    }, 10);
                    showToast('Skill added successfully!', 'green');
                    skillInput.value = '';
                } else {
                    showToast('Failed to add skill.', 'red');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('An error occurred.', 'red');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnContent;
            });
        });
    });

    function showToast(message, color = 'green') {
        const toast = document.createElement('div');
        toast.textContent = message;
        toast.className = `fixed top-5 right-5 bg-${color}-500 text-white px-4 py-2 rounded shadow-md z-50 animate-fade-in-up`;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.classList.add('opacity-0', 'transition-opacity');
            setTimeout(() => toast.remove(), 1000);
        }, 2000);
    }
</script>

<style>
@keyframes fade-in-up {
    0% { opacity: 0; transform: translateY(10px); }
    100% { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up {
    animation: fade-in-up 0.3s ease-out forwards;
}
</style>
@endsection
