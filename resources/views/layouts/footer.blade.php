<footer class="bg-gray-800 text-gray-300 mt-16">
    <div class="max-w-7xl mx-auto px-6 py-10 grid grid-cols-1 md:grid-cols-3 gap-8 text-sm">

        <!-- Brand Info -->
        <div>
            <h4 class="text-lg font-semibold text-white mb-2">JobFastHired</h4>
            <p>Optimizing part-time job searches for students using Google Maps integration.</p>
        </div>

        <!-- Navigation Links -->
        <div>
            <h4 class="text-lg font-semibold text-white mb-2">Quick Links</h4>
            <ul class="space-y-2">
                <li class="flex items-center gap-2">
                    <svg data-lucide="briefcase" class="w-4 h-4 stroke-gray-400"></svg>
                    <a href="{{ route('job.search') }}" class="hover:text-blue-400">Browse Jobs</a>
                </li>
                <li class="flex items-center gap-2">
                    <svg data-lucide="log-in" class="w-4 h-4 stroke-gray-400"></svg>
                    <a href="{{ route('login') }}" class="hover:text-blue-400">Login</a>
                </li>
                <li class="flex items-center gap-2">
                    <svg data-lucide="mail" class="w-4 h-4 stroke-gray-400"></svg>
                    <a href="#" class="hover:text-blue-400">Contact Us</a>
                </li>
            </ul>
        </div>

        <!-- Legal + Social Media -->
        <div>
            <h4 class="text-lg font-semibold text-white mb-2">Legal & More</h4>
            <ul class="space-y-2 mb-4">
                <li class="flex items-center gap-2">
                    <svg data-lucide="file-text" class="w-4 h-4 stroke-gray-400"></svg>
                    <a href="#" class="hover:text-blue-400">Terms & Conditions</a>
                </li>
                <li class="flex items-center gap-2">
                    <svg data-lucide="shield" class="w-4 h-4 stroke-gray-400"></svg>
                    <a href="#" class="hover:text-blue-400">Privacy Policy</a>
                </li>
            </ul>

            {{-- Social Media Icons (Linked Later) --}}
            <div class="flex gap-4">
                <a href="#" class="hover:text-blue-400" aria-label="Facebook">
                    <svg data-lucide="facebook" class="w-5 h-5 stroke-gray-300"></svg>
                </a>
                <a href="#" class="hover:text-blue-400" aria-label="Twitter">
                    <svg data-lucide="twitter" class="w-5 h-5 stroke-gray-300"></svg>
                </a>
                <a href="#" class="hover:text-blue-400" aria-label="Instagram">
                    <svg data-lucide="instagram" class="w-5 h-5 stroke-gray-300"></svg>
                </a>
            </div>
        </div>
    </div>

    <div class="bg-gray-700 py-4 text-center text-xs text-gray-400">
        &copy; {{ date('Y') }} JobFastHired. All rights reserved.
    </div>
</footer>
