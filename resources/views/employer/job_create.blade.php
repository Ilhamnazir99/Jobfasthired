@extends('layouts.app')

@section('content')
<div class="flex justify-center mt-10">
    <div class="w-full max-w-2xl bg-white shadow-md rounded-lg p-8">

        {{-- Title --}}
        <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Post a New Job</h2>

        <form id="jobForm" action="{{ route('job.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Job Title --}}
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Job Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" class="w-full border border-gray-300 px-4 py-2 rounded" required>
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Description <span class="text-red-500">*</span></label>
                <textarea name="description" rows="4" class="w-full border border-gray-300 px-4 py-2 rounded" required></textarea>
            </div>

            {{-- Location --}}
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Location (City / Area) <span class="text-red-500">*</span></label>
                <input type="text" name="location" class="w-full border border-gray-300 px-4 py-2 rounded" required>
            </div>

            {{-- Address --}}
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Full Address <span class="text-red-500">*</span></label>
                <input type="text" name="address" id="address" class="w-full border border-gray-300 px-4 py-2 rounded" required placeholder="123 Jalan Example, 43000 Kajang, Selangor">
            </div>

            {{-- Hidden Lat/Lng --}}
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">

            {{-- Salary --}}
<div>
    <label class="block text-gray-700 font-semibold mb-2">Salary Rate <span class="text-red-500">*</span></label>
    <div class="flex items-center gap-2">
        <input type="number" step="0.01" name="salary" class="w-full border border-gray-300 px-4 py-2 rounded" required placeholder="e.g. 20">
        <span class="text-gray-600">RM/hour</span>
    </div>
</div>


            {{-- Schedule --}}
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Schedule <span class="text-red-500">*</span></label>
                @php $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday']; @endphp
                @foreach($days as $day)
@php $dayKey = strtolower($day); @endphp
<div class="flex items-center gap-4 mb-2">
    <label class="w-24">{{ $day }}</label>
    <input type="checkbox"
           name="schedule[{{ $dayKey }}][active]"
           id="check-{{ $dayKey }}"
           value="1"
           class="form-checkbox h-5 w-5 text-blue-600">
    
    <input type="time"
           name="schedule[{{ $dayKey }}][start]"
           id="start-{{ $dayKey }}"
           class="border border-gray-300 px-2 py-1 rounded">

    <input type="time"
           name="schedule[{{ $dayKey }}][end]"
           id="end-{{ $dayKey }}"
           class="border border-gray-300 px-2 py-1 rounded">
</div>
@endforeach

                <p class="text-sm text-gray-500 mt-1">Only fill times for active days.</p>
            </div>

            {{-- Skills --}}
            <div>
               <label class="block text-gray-700 font-semibold mb-2">Skills (Optional)</label>
                <div id="skill-tags" class="flex flex-wrap gap-2 mb-2"></div>
                <div class="flex gap-2">
                    <input type="text" id="new-skill" class="flex-grow border border-gray-300 px-3 py-2 rounded" placeholder="Enter skill (e.g. Laravel)">
                    <button type="button" onclick="addSkill()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">Add Skill</button>
                </div>
            </div>

            {{-- Submit --}}
            <div class="text-right">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded shadow">Post Job</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('jobForm');
    const addressInput = document.getElementById('address');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');

    // Schedule: Enable/disable time inputs based on checkbox
    const scheduleDays = ['monday','tuesday','wednesday','thursday','friday','saturday','sunday'];
    scheduleDays.forEach(day => {
        const checkbox = document.getElementById(`check-${day}`);
        const start = document.getElementById(`start-${day}`);
        const end = document.getElementById(`end-${day}`);

        const toggleInputs = () => {
            const isActive = checkbox.checked;
            start.disabled = !isActive;
            end.disabled = !isActive;
        };

        checkbox.addEventListener('change', toggleInputs);
        toggleInputs(); // run on load
    });

    // Main form submission handler
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        // ✅ Validate schedule time inputs
        let scheduleValid = true;
        for (let day of scheduleDays) {
            const checkbox = document.getElementById(`check-${day}`);
            const start = document.getElementById(`start-${day}`);
            const end = document.getElementById(`end-${day}`);

          if (checkbox && checkbox.checked) {
    if (!start.value || !end.value) {
        alert(`Please fill in both start and end times for ${day.charAt(0).toUpperCase() + day.slice(1)}.`);
        scheduleValid = false;
        break;
    }

    // ✅ New: Check that end time is after start time
    const [startH, startM] = start.value.split(':').map(Number);
    const [endH, endM] = end.value.split(':').map(Number);
    const startMinutes = startH * 60 + startM;
    const endMinutes = endH * 60 + endM;

    if (endMinutes <= startMinutes) {
        alert(`End time must be after start time for ${day.charAt(0).toUpperCase() + day.slice(1)}.`);
        scheduleValid = false;
        break;
    }
}

        }
        if (!scheduleValid) return;

        // ✅ Continue with address geocoding
        const address = addressInput.value.trim();
        if (!address) {
            alert("Please enter a valid address.");
            return;
        }

        const apiKey = "{{ $googleApiKey }}";
        const url = `https://maps.googleapis.com/maps/api/geocode/json?address=${encodeURIComponent(address)}&key=${apiKey}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.status === "OK" && data.results.length > 0) {
                    const location = data.results[0].geometry.location;
                    latInput.value = location.lat;
                    lngInput.value = location.lng;
                    form.submit(); // ✅ All good, submit
                } else {
                    alert("Address not found. Try a more specific address.");
                }
            })
            .catch(error => {
                alert("Error getting geolocation.");
                console.error(error);
            });
    });
});

// SKILL TAG FUNCTIONALITY
let skillList = [];

function addSkill() {
    const input = document.getElementById('new-skill');
    const skill = input.value.trim();

    if (!skill || skillList.includes(skill.toLowerCase())) return;

    skillList.push(skill.toLowerCase());

    const tag = document.createElement('span');
    tag.className = 'bg-gray-200 text-gray-800 px-3 py-1 rounded-full text-sm flex items-center gap-2';
    tag.innerHTML = `
        ${skill}
        <button type="button" onclick="removeSkill(this, '${skill.toLowerCase()}')" class="ml-2 text-red-600">&times;</button>
        <input type="hidden" name="skills[]" value="${skill}">
    `;
    tag.dataset.skill = skill;
    document.getElementById('skill-tags').appendChild(tag);
    input.value = '';
}

function removeSkill(button, skillName) {
    skillList = skillList.filter(skill => skill !== skillName.toLowerCase());
    button.parentElement.remove();
}
</script>
@endsection
