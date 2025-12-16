<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem Inventaris</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
        <div class="flex items-center justify-center mb-6">
            <img src="https://cdn-icons-png.flaticon.com/512/2921/2921222.png" alt="Logo" class="w-10 h-10 mr-2">
            <h1 class="text-2xl font-bold text-indigo-600">Sistem Inventaris</h1>
        </div>

        @if(session('error'))
            <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.process') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Email</label>
                <input type="email" name="email" class="w-full border border-gray-300 rounded p-2 focus:ring focus:ring-indigo-200" required>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 mb-2">Password</label>
                <input type="password" name="password" class="w-full border border-gray-300 rounded p-2 focus:ring focus:ring-indigo-200" required>
            </div>

            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded font-semibold">Login</button>
        </form>
    </div>

</body>
</html>
