<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Lucide Icons -->
    <!-- <script src="https://unpkg.com/lucide@latest"></script> -->
    <script>
        // Initialize Lucide when DOM is ready and after dynamic content loads
        // document.addEventListener('DOMContentLoaded', function() {
        //     // First initialization
        //     // lucide.createIcons();
        //     createIcons({ icons }); 
            
        //     // Create observer to watch for dynamically added content
        //     const observer = new MutationObserver(function(mutations) {
        //         mutations.forEach(function(mutation) {
        //             if (mutation.addedNodes.length) {
        //                 // Small delay to ensure DOM is ready
        //                 setTimeout(() => {
        //                     // lucide.createIcons();
        //                     createIcons({ icons }); 
        //                 }, 100);
        //             }
        //         });
        //     });

        //     // Start observing the document body for added nodes
        //     observer.observe(document.body, {
        //         childList: true,
        //         subtree: true
        //     });
        // });


        
    </script>

    <!-- Intro.js Styles & Script -->
<link href="https://unpkg.com/intro.js/minified/introjs.min.css" rel="stylesheet">
<script src="https://unpkg.com/intro.js/minified/intro.min.js"></script>



</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-white-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>

        

        @yield('scripts')
    </div>
</body>
   {{-- ✅ Include Footer --}}
        @include('layouts.footer')
</html>