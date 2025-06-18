@extends('layouts.app')

@section('content')

{{-- HERO + SEARCH --}}
<div class="relative px-6 py-16 bg-cover bg-center bg-no-repeat"
    style="background-image: url({{ asset('storage/my-background.jpg') }});">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-10 items-center text-white">
        <!-- TEXT + SEARCH -->
        <div>
            <h2 class="text-4xl font-extrabold mb-4 leading-tight">
                Find a job that suits<br>your interests and skills.
            </h2>
            <p class="mb-8 text-white/90">
                Thousands of part-time jobs are waiting for you. Search by location or keyword.
            </p>

            <!-- Search Form -->
            <form id="job-search-form" action="{{ route('student.dashboard')}}" method="GET"
                class="flex flex-col md:flex-row md:items-center gap-4">
                <input type="text" name="title" placeholder="Job title..." value="{{ request('title') }}"
                    class="w-full md:w-1/3 p-3 border rounded-md shadow-sm focus:ring focus:ring-blue-200 text-black">

                <div class="relative w-full md:w-2/3">
                    <input type="text" id="location-input" name="location" placeholder="Location..."
                        value="{{ request('location') }}"
                        class="w-full p-3 pr-10 border rounded-md shadow-sm focus:ring focus:ring-blue-200 text-black">

                    <button type="button" id="use-location-btn"
                        x-data="{ show: false, timeout: null }"
                        x-on:mouseenter="show = true; clearTimeout(timeout); timeout = setTimeout(() => show = false, 2000)"
                        class="absolute right-3 top-1/2 -translate-y-1/2 transform group"
                        aria-label="Use your location">
                        <svg data-lucide="map-pin" class="w-5 h-5 stroke-blue-500 transition-colors duration-200"></svg>
                        <div x-show="show"
                            x-transition
                            class="absolute bottom-full mb-2 bg-black text-white text-xs px-2 py-1 rounded shadow whitespace-nowrap z-10">
                            Your Location
                        </div>
                    </button>
                </div>

                <button type="button" id="search-btn"
                    class="bg-blue-600 text-white px-6 py-3 rounded-md shadow hover:bg-blue-700 transition">
                    Search
                </button>
            </form>

            <div id="suggestions" class="mt-3 hidden bg-white border rounded shadow-md p-3 text-sm text-black"></div>
        </div>

        <!-- IMAGE -->
        <div class="flex justify-center">
            <img src="{{ asset('storage/job-fair-poster.png') }}" alt="Job Search Illustration" class="w-full max-w-md">
        </div>
    </div>
</div>

{{-- JOB LISTINGS + MAP LAYOUT --}}
<div class="max-w-7xl mx-auto px-6 mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">

    <!-- Job Listings -->
    <div class="md:col-span-1 overflow-y-auto h-[500px] space-y-4">
        <h2 class="text-xl font-semibold mb-4">Available Part-Time Jobs</h2>
        <div id="job-list" class="space-y-4">
            <div id="blade-job-list">
                @forelse($jobs as $job)
                
                @empty
                <p>No jobs found.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Map Section -->
    <div class="md:col-span-2">
        <h2 class="text-xl font-semibold mb-4">Map View</h2>

        <div class="relative rounded overflow-hidden border" style="height: 500px;">
            <div id="map" class="absolute inset-0 z-0"></div>

            <div id="radius-slider-container"
                style="position: absolute; bottom: 16px; left: 50%; transform: translateX(-50%);
                        background: white; padding: 6px 10px; border-radius: 8px;
                        box-shadow: 0 5px 15px rgba(0,0,0,0.15); width: 16rem; z-index: 999; text-align: center;">
                <label for="radiusRange" class="text-sm font-medium">
                    Search Radius: <span id="radiusValue">5</span> km
                </label>
                <input type="range" min="1" max="30" value="5" id="radiusRange" class="w-full mt-1">
            </div>
        </div>

        <div id="job-details" class="mt-6 p-4 bg-white shadow rounded hidden">
            <h3 class="text-lg font-bold" id="job-title">Job Title</h3>
            <p class="text-sm text-gray-600" id="job-location">Location</p>
            <p class="text-sm text-gray-600" id="job-salary">Salary: RM 0.00</p>
            <button class="mt-3 bg-blue-600 text-white px-4 py-2 rounded" id="apply-btn">Apply Now</button>
        </div>
    </div>

</div>

@endsection



@section('scripts')
<script>
    let map;
    let markers = [];
    let userLocationMarker;
    let userLocationRadius;
    const jobs = @json($jobs);
    const defaultRadius = 5000; // 5 km radius
    let currentRadius = defaultRadius;


    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            center: {
                lat: 3.1390,
                lng: 101.6869
            }, // fallback: KL
            zoom: 10,
        });

        // Call location logic after map is initialized
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    console.log("User location:", lat, lng);

                    showUserLocationOnMap(lat, lng); // ✅ draw circle + marker
                    // plotAllJobs(lat, lng); // ✅ you can now filter based on user location
                },
                function(error) {
                    console.warn("Geolocation error:", error.message);
                    // plotAllJobs(); // fallback to all jobs if user denies
                }
            );
        } else {
            console.warn("Geolocation not supported");
            // plotAllJobs();
        }
    }

    // function plotAllJobs() {
    //     markers.forEach(markerData => markerData.marker.setMap(null));
    //     markers = [];

    //     const jobList = document.getElementById("job-list");
    //     jobList.innerHTML = ""; // Clear job cards to avoid duplication and hide Blade fallback

    //     const bladeFallback = document.getElementById("blade-job-list");
    //     if (bladeFallback) bladeFallback.style.display = "none";

    //     jobs.forEach((job) => {
    //         if (job.latitude && job.longitude) {
    //             const marker = new google.maps.Marker({
    //                 position: {
    //                     lat: parseFloat(job.latitude),
    //                     lng: parseFloat(job.longitude)
    //                 },
    //                 map: map,
    //                 title: job.title,
    //             });

    //             markers.push({
    //                 id: job.id,
    //                 marker: marker
    //             });

    //             marker.addListener('click', () => {
    //                 showJobDetails(job);
    //                 highlightJobCard(job.id);
    //                 map.panTo(marker.getPosition());
    //             });
    //             renderJobCard(job);

    //         }
    //     });
    // }

    function renderJobCard(job) {
        const jobList = document.getElementById("job-list");

        const jobCard = document.createElement("div");
        jobCard.className = "p-4 border rounded shadow-sm job-card cursor-pointer hover:bg-blue-50 transition";
        jobCard.dataset.jobId = job.id;

        jobCard.innerHTML = `
            <h3 class="text-lg font-bold">${job.title}</h3>

            ${job.company_name ? `
                <p class="text-sm text-gray-500">${job.company_name}</p>
            ` : ''}

            <div class="flex items-center text-sm text-gray-600 mt-1">
                <svg data-lucide="map-pin" class="w-4 h-4 mr-1 stroke-gray-500"></svg>
                <span>${job.location ?? 'Location not specified'}</span>
            </div>

            ${job.salary ? `
                <div class="flex items-center text-sm text-gray-600 mt-1">
                    <svg data-lucide="dollar-sign" class="w-4 h-4 mr-1 stroke-green-600"></svg>
                    <span>RM ${parseFloat(job.salary).toFixed(2)} per hour</span>
                </div>
            ` : ''}

            ${job.schedule && typeof job.schedule === 'object' && !Array.isArray(job.schedule) ? `
        <div class="flex items-start text-sm text-gray-600 mt-1">
            <svg data-lucide="calendar-clock" class="w-4 h-4 mr-1 mt-1 stroke-blue-500"></svg>
            <div class="space-y-1">
                ${Object.entries(job.schedule).map(([day, times]) => {
                    if (times.start && times.end) {
                        return `<div><strong>${day.charAt(0).toUpperCase() + day.slice(1)}:</strong> ${times.start} - ${times.end}</div>`;
                    }
                    return '';
                }).join('')}
            </div>
        </div>
    ` : ''}


            ${job.weekly_pay ? `
                <div class="flex items-center text-sm text-gray-600 mt-1">
                    <svg data-lucide="wallet" class="w-4 h-4 mr-1 stroke-yellow-600"></svg>
                    <span>Est. Weekly Pay: RM ${job.weekly_pay}</span>
                </div>
            ` : ''}
        `;

        jobCard.onclick = () => showJobOnMap(job.id);
        jobList.appendChild(jobCard);

        window.lucide?.createIcons({
            icons: window.lucide?.icons
        });
    }


    function showJobOnMap(jobId) {
        const job = jobs.find(j => j.id === jobId);
        if (job) {
            const markerData = markers.find(m => m.id === jobId);
            if (markerData) {
                map.panTo(markerData.marker.getPosition());
                markerData.marker.setAnimation(google.maps.Animation.BOUNCE);
                setTimeout(() => markerData.marker.setAnimation(null), 1400);
            }
            showJobDetails(job);
            highlightJobCard(jobId);
        }
    }

    function showJobDetails(job) {
        if (!job) return;
        document.getElementById('job-details').classList.remove('hidden');
        document.getElementById('job-title').innerText = job.title;
        document.getElementById('job-location').innerText = job.location ?? 'Location not specified';
        document.getElementById('job-salary').innerText = "Salary: RM " + (job.salary ?? 'Not specified');
        document.getElementById('apply-btn').onclick = function() {
        window.location.href = `/student/jobs/${job.id}/apply`;
               

      };
    }

    function highlightJobCard(jobId) {
        document.querySelectorAll('.job-card').forEach(card => {
            card.classList.remove('bg-blue-100', 'border-blue-500');
        });

        const selectedCard = document.querySelector(`.job-card[data-job-id="${jobId}"]`);
        if (selectedCard) {
            selectedCard.classList.add('bg-blue-100', 'border-blue-500');
        }
    }

    function showUserLocationOnMap(lat, lng) {
        const userPosition = new google.maps.LatLng(lat, lng);

        if (userLocationMarker) userLocationMarker.setMap(null);
        if (userLocationRadius) userLocationRadius.setMap(null);

        userLocationMarker = new google.maps.Marker({
            position: userPosition,
            map: map,
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 8,
                fillColor: "#007bff",
                fillOpacity: 0.8,
                strokeWeight: 0
            },
            title: "Your Location"
        });

        userLocationRadius = new google.maps.Circle({
            center: userPosition,
            radius: defaultRadius,
            map: map,
            strokeColor: "#007bff",
            strokeOpacity: 0.5,
            strokeWeight: 1,
            fillColor: "#007bff",
            fillOpacity: 0.1,
        });

        map.setCenter(userPosition);
        map.setZoom(12);

        filterJobsByRadius(lat, lng, defaultRadius);
    }

    function filterJobsByRadius(lat, lng, radius) {
        const titleInput = document.querySelector('input[name="title"]').value.trim().toLowerCase();
        const userLatLng = new google.maps.LatLng(lat, lng);

        const filteredJobs = jobs.filter((job) => {
            const isNearby = job.latitude && job.longitude && (
                google.maps.geometry.spherical.computeDistanceBetween(
                    userLatLng,
                    new google.maps.LatLng(job.latitude, job.longitude)
                ) <= radius
            );

            const matchesTitle = titleInput === "" || job.title.toLowerCase().startsWith(titleInput);

            const result = isNearby && matchesTitle;
            return result;

        });

        displayFilteredJobs(filteredJobs);

    }


    function displayFilteredJobs(filteredJobs) {
        const jobList = document.getElementById("job-list");
        jobList.innerHTML = "";

        markers.forEach(markerData => markerData.marker.setMap(null));
        markers = [];

        filteredJobs.forEach((job) => {
            renderJobCard(job);

            const marker = new google.maps.Marker({
                position: {
                    lat: parseFloat(job.latitude),
                    lng: parseFloat(job.longitude)
                },
                map: map,
                title: job.title,
            });

            markers.push({
                id: job.id,
                marker: marker
            });

            marker.addListener('click', () => {
                showJobDetails(job);
                highlightJobCard(job.id);
                map.panTo(marker.getPosition());
            });
        });

        if (filteredJobs.length === 0) {
            jobList.innerHTML = "<p>No jobs found within this radius.</p>";
        }
    }

    // Location detection and search
    document.getElementById('use-location-btn').addEventListener('click', function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;

                    // Show marker on map
                    showUserLocationOnMap(latitude, longitude);

                    // Convert lat/lng to address (reverse geocode)
                    const geocoder = new google.maps.Geocoder();
                    geocoder.geocode({
                        location: {
                            lat: latitude,
                            lng: longitude
                        }
                    }, function(results, status) {
                        if (status === "OK" && results.length) {
                            // Skip addresses with Plus Codes or "Unnamed Road" / "Jalan Tanpa Nama"
                            const filtered = results.find(result =>
                                !result.formatted_address.match(/^[A-Z0-9]{4,}\+/) &&
                                !result.formatted_address.toLowerCase().includes("unnamed road") &&
                                !result.formatted_address.toLowerCase().includes("jalan tanpa nama")
                            );

                            const formattedAddress = filtered ? filtered.formatted_address : results[0].formatted_address;
                            document.getElementById('location-input').value = formattedAddress;
                            
                        } else {
                            alert("Failed to get your address from location.");
                        }
                    });
                },
                (error) => {
                    alert("Unable to access your location. Please allow location access.");
                }
            );
        } else {
            alert("Geolocation is not supported by your browser.");
        }
    });

    document.getElementById('search-btn').addEventListener('click', function() {
        const title = document.querySelector('input[name="title"]').value.trim();
        const location = document.querySelector('input[name="location"]').value.trim();
        const radius = currentRadius;

        if (!location) {
            alert("Please enter a location or use 'Your Location'.");
            return;
        }

        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({
            address: location
        }, function(results, status) {
            if (status === "OK" && results[0]) {
                const lat = results[0].geometry.location.lat();
                const lng = results[0].geometry.location.lng();

                showUserLocationOnMap(lat, lng); // Show location on map

                // ✅ Fetch filtered jobs from backend (force JSON response)
                fetch(`/job-search?title=${encodeURIComponent(title)}&location=${encodeURIComponent(location)}&lat=${lat}&lng=${lng}&radius=${radius / 1000}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => {
                        if (!res.ok) throw new Error("Network response was not OK");
                        return res.json();
                    })
                    .then(data => {
                        jobs.splice(0, jobs.length, ...data.jobs); // update global job list
                        // plotAllJobs(); // re-render markers + cards
                        filterJobsByRadius(lat, lng, radius); // re-filter by radius
                    })
                    .catch(err => {
                        console.error("❌ Failed to fetch jobs:", err);
                        alert("Something went wrong while searching.");
                    });
            } else {
                alert("Location not found.");
            }
        });
    });



    // AJAX-based job title suggestions
    document.addEventListener("DOMContentLoaded", function() {
        const titleInput = document.querySelector('input[name="title"]');
        const suggestionBox = document.getElementById("suggestions");
        const radiusSlider = document.getElementById("radiusRange");
        const radiusValueLabel = document.getElementById("radiusValue");

        // 1. AUTOCOMPLETE for job title
        titleInput.addEventListener("input", function() {
            let query = this.value.trim();
            if (query.length < 2) {
                suggestionBox.classList.add('hidden');
                return;
            }

            fetch(`/job-search/suggestions?query=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    suggestionBox.innerHTML = "";
                    if (data.length) {
                        suggestionBox.classList.remove('hidden');
                        data.forEach(item => {
                            const suggestion = document.createElement("div");
                            suggestion.textContent = item.title;
                            suggestion.classList.add("p-2", "cursor-pointer");
                            suggestion.onclick = function() {
                                titleInput.value = item.title;
                                suggestionBox.classList.add('hidden');
                            };
                            suggestionBox.appendChild(suggestion);
                        });
                    } else {
                        suggestionBox.classList.add('hidden');
                    }
                });
        });

        // Hide suggestions if clicking outside
        document.addEventListener("click", function(e) {
            if (!suggestionBox.contains(e.target) && e.target !== titleInput) {
                suggestionBox.classList.add('hidden');
            }
        });

        // 2. RADIUS SLIDER update
        radiusSlider.addEventListener("input", function() {
            const km = parseInt(this.value);
            radiusValueLabel.textContent = km;
            currentRadius = km * 1000;

            if (userLocationMarker) {
                const position = userLocationMarker.getPosition();
                userLocationRadius.setRadius(currentRadius);
                filterJobsByRadius(position.lat(), position.lng(), currentRadius);
            }
        });

        // 3. PREVENT FORM SUBMIT + TRIGGER AJAX SEARCH
        document.getElementById("job-search-form").addEventListener("submit", function(e) {
            e.preventDefault(); // prevent page reload
            document.getElementById("search-btn").click(); // trigger the search manually
        });

        // 4. AJAX SEARCH BTN — already exists in your code, but we enhance it here:
        document.getElementById('search-btn').addEventListener('click', function() {
            const title = document.querySelector('input[name="title"]').value.trim();
            const location = document.querySelector('input[name="location"]').value.trim();
            const radius = currentRadius;

            if (!location) {
                alert("Please enter a location or use 'Your Location'.");
                return;
            }

            const geocoder = new google.maps.Geocoder();
            geocoder.geocode({
                address: location
            }, function(results, status) {
                if (status === "OK" && results[0]) {
                    const lat = results[0].geometry.location.lat();
                    const lng = results[0].geometry.location.lng();

                    showUserLocationOnMap(lat, lng); // Mark on map

                    // 🔥 AJAX call to get jobs
                    fetch(`/job-search?title=${encodeURIComponent(title)}&location=${encodeURIComponent(location)}&lat=${lat}&lng=${lng}&radius=${radius / 1000}`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => {
                            if (!res.ok) throw new Error("Network response was not OK");
                            return res.json();
                        })
                        .then(data => {
                            // Update URL without reload
                            const newUrl = `/job-search?title=${encodeURIComponent(title)}&location=${encodeURIComponent(location)}&lat=${lat}&lng=${lng}&radius=${radius / 1000}`;
                            history.pushState(null, "", newUrl);

                            // Replace job list globally
                            jobs.splice(0, jobs.length, ...data.jobs);
                            // plotAllJobs();
                            filterJobsByRadius(lat, lng, radius);
                        })
                        .catch(err => {
                            console.error("❌ Failed to fetch jobs:", err);
                            alert("Something went wrong while searching.");
                        });
                } else {
                    alert("Location not found.");
                }
            });
        });

    });


    window.initMap = initMap;
</script>


<!-- Google Maps API with geometry library -->
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.key') }}&libraries=places,geometry&callback=initMap">
</script>

@endsection