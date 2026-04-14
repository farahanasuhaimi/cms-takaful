<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login · Dr Takaful CMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-matcha-50 font-sans min-h-screen flex items-center justify-center">

    <div class="w-full max-w-sm">

        {{-- Brand --}}
        <div class="text-center mb-8">
            <h1 class="text-2xl font-semibold text-matcha-800">Dr Takaful</h1>
            <p class="text-sm text-matcha-600 mt-1">Client Management System</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-matcha-100 px-8 py-8">

            @if (session('status'))
                <div class="mb-4 text-sm text-matcha-700 bg-matcha-50 border border-matcha-200 rounded-lg px-4 py-2">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           required autofocus autocomplete="username"
                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-matcha-400 focus:border-matcha-400 @error('email') border-red-400 @enderror" />
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mt-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input id="password" type="password" name="password"
                           required autocomplete="current-password"
                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-matcha-400 focus:border-matcha-400 @error('password') border-red-400 @enderror" />
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember --}}
                <div class="mt-4 flex items-center">
                    <input id="remember_me" type="checkbox" name="remember"
                           class="rounded border-gray-300 text-matcha-600 focus:ring-matcha-400" />
                    <label for="remember_me" class="ml-2 text-sm text-gray-600">Remember me</label>
                </div>

                {{-- Submit --}}
                <button type="submit"
                        class="mt-6 w-full bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium py-2.5 rounded-lg transition">
                    Log in
                </button>

            </form>
        </div>

    </div>

</body>
</html>
