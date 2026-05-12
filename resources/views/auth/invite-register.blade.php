<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create Account · Dr Takaful CMS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-matcha-50 font-sans min-h-screen flex items-center justify-center">

    <div class="w-full max-w-sm">

        {{-- Brand --}}
        <div class="text-center mb-8">
            <h1 class="text-2xl font-semibold text-matcha-800">Dr Takaful</h1>
            <p class="text-sm text-matcha-600 mt-1">You've been invited to join</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-matcha-100 px-8 py-8">

            <p class="text-sm text-gray-500 mb-5">
                Setting up account for <strong class="text-gray-700">{{ $invitation->email }}</strong>
            </p>

            <form method="POST" action="{{ route('invite.register', $invitation->token) }}">
                @csrf

                {{-- Name --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Your Name</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}"
                           required autofocus autocomplete="name"
                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-matcha-400 focus:border-matcha-400 @error('name') border-red-400 @enderror" />
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mt-4">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input id="password" type="password" name="password"
                           required autocomplete="new-password"
                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-matcha-400 focus:border-matcha-400 @error('password') border-red-400 @enderror" />
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirm password --}}
                <div class="mt-4">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input id="password_confirmation" type="password" name="password_confirmation"
                           required autocomplete="new-password"
                           class="w-full rounded-lg border-gray-300 text-sm focus:ring-matcha-400 focus:border-matcha-400" />
                </div>

                <button type="submit"
                        class="mt-6 w-full bg-matcha-600 hover:bg-matcha-800 text-white text-sm font-medium py-2.5 rounded-lg transition">
                    Create Account
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-4">
            This invite link expires {{ $invitation->expires_at->diffForHumans() }}.
        </p>

    </div>

</body>
</html>
