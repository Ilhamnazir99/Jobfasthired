<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Login</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f9fafb;
        }

        .card {
            background: white;
            width: 100%;
            max-width: 460px;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
        }

        .card-description {
            color: #6b7280;
            font-size: 0.9rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .tabs-list {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .tab-button {
            flex: 1;
            padding: 0.5rem;
            background: #e5e7eb;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 600;
        }

        .tab-button.active {
            background: #3b82f6;
            color: white;
        }

        .form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .btn {
            background-color: #3b82f6;
            color: white;
            padding: 0.75rem;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            font-weight: 600;
        }

        .btn:hover {
            background-color: #2563eb;
        }

       .footer-text {
    font-size: 0.85rem;
    text-align: center;
    margin-top: 1rem;
    color: #6b7280;
}

.footer-text a {
    color: #3b82f6;
}

.footer-text a:hover {
    text-decoration: underline;
}

         .icon-circle {
            background: #dbeafe;
            color: #3b82f6;
            border-radius: 50%;
            padding: 0.5rem 0.75rem;
            font-size: 1.25rem;
            margin: 0 auto 1rem auto;
            display: inline-block;
        }
    </style>
</head>
<body class="bg-gray-50 flex justify-center items-center min-h-screen">
    <div class="card">
        {{ $slot }}
    </div>
</body>
</html>
