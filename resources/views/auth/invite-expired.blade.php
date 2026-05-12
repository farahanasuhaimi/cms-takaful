<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Invite Expired · Dr Takaful CMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-matcha-50 font-sans min-h-screen flex items-center justify-center">

    <div class="w-full max-w-sm text-center">
        <h1 class="text-2xl font-semibold text-matcha-800">Dr Takaful</h1>
        <div class="mt-8 bg-white rounded-xl shadow-sm border border-matcha-100 px-8 py-8">
            <p class="text-gray-700 font-medium">This invite link is no longer valid.</p>
            <p class="text-sm text-gray-400 mt-2">It may have expired or already been used. Ask your administrator for a new link.</p>
            <a href="{{ route('login') }}" class="mt-6 inline-block text-sm text-matcha-600 hover:underline">Back to login</a>
        </div>
    </div>

</body>
</html>
