<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.3.2/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center">
    <div class="bg-white p-8 shadow-md rounded w-96">
        <h2 class="text-xl font-bold mb-4 text-center text-blue-600">Admin Login</h2>

        @if($errors->any())
            <div class="text-red-500 text-sm mb-4">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ url('/admin/login') }}">
            @csrf
            <div class="mb-4">
                <label>Email</label>
                <input type="email" name="email" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="mb-4">
                <label>Password</label>
                <input type="password" name="password" class="w-full border rounded px-3 py-2" required>
            </div>
            <button class="w-full bg-blue-600 text-white py-2 rounded">Login</button>
        </form>
    </div>
</body>
</html>
