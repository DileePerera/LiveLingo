<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Lingo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="{{ asset('images/logo.svg') }}">
</head>

<body class="min-h-screen bg-gray-900 text-gray-100">
    <div class="max-w-6xl mx-auto px-4 py-16">
        <!-- Navbar -->
        <nav class="flex justify-between items-center mb-16">
            <div class="flex items-center space-x-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200">

                    <!-- Video Camera Shape -->
                    <path d="M50 70 L120 70 L120 130 L50 130 L50 70 Z" fill="#3B82F6" stroke="#60A5FA"
                        stroke-width="4" />

                    <!-- Camera Lens -->
                    <circle cx="85" cy="100" r="20" fill="#1E293B" stroke="#60A5FA" stroke-width="4" />

                    <!-- Video Light Indicator -->
                    <circle cx="85" cy="100" r="8" fill="#60A5FA" />

                    <!-- Video Signal Lines -->
                    <path d="M120 85 L150 70 L150 130 L120 115" fill="#3B82F6" stroke="#60A5FA" stroke-width="4" />

                    <!-- Connection Lines -->
                    <path d="M30 90 L40 90" stroke="#60A5FA" stroke-width="4" />
                    <path d="M30 100 L40 100" stroke="#60A5FA" stroke-width="4" />
                    <path d="M30 110 L40 110" stroke="#60A5FA" stroke-width="4" />
                </svg>
                <span class="text-2xl font-bold">Live Lingo</span>
            </div>
            <div class="space-x-4">
                <a href="{{ route('log_index') }}" class="text-gray-300 hover:text-white">Login</a>
                <a href="{{ route('reg_index') }}" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded-lg">Sign Up</a>
            </div>
        </nav>

        <!-- Hero Section -->
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <h1 class="text-5xl font-bold mb-6">Connect Seamlessly, Meet Effortlessly</h1>
                <p class="text-gray-400 text-xl mb-8">
                    Experience crystal-clear video conferences with multilingual translation ability.
                    Perfect for teams of any size.
                </p>
                <div class="space-x-4">
                    <!-- <a href="#" class="bg-blue-600 hover:bg-blue-700 text-lg px-8 py-6 rounded-lg">Start Free -->
                        <!-- Trial</a> -->
                    <!-- <a href="#" class="border border-gray-300 text-lg px-8 py-6 rounded-lg">Watch Demo</a> -->
                </div>
            </div>
            <div class="relative">
                <img src="https://visionable.com/wp-content/uploads/2021/07/iStock-1217489268-scaled.jpg"
                    alt="Video conference screenshot" class="rounded-lg shadow-2xl">
            </div>
        </div>

        <!-- Features Section -->
        <div class="mt-24 grid md:grid-cols-3 gap-8">
            <div class="bg-gray-800 p-6 rounded-lg">
                <svg class="h-12 w-12 text-blue-500 mb-4" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18m-7 5h7" />
                </svg>
                <h3 class="text-xl font-bold mb-2">Team Collaboration</h3>
                <p class="text-gray-400">Host meetings with up to 100 participants with HD video quality.</p>
            </div>
            <div class="bg-gray-800 p-6 rounded-lg">
                <svg class="h-12 w-12 text-blue-500 mb-4" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 11c3.5 0 4-2 4-4V5c0-2-1.5-3-4-3s-4 1-4 3v2c0 2 1 4 4 4zM12 13c-4.4 0-8 2.5-8 6v1h16v-1c0-3.5-3.6-6-8-6z" />
                </svg>
                <h3 class="text-xl font-bold mb-2">Live Translation</h3>
                <p class="text-gray-400">Sinhala and Tamil live translation .
                </p>
            </div>
            <div class="bg-gray-800 p-6 rounded-lg">
                <svg class="h-12 w-12 text-blue-500 mb-4" fill="none" stroke="currentColor" stroke-width="2"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 10l-6-6m6 6l-6 6m6-6H3" />
                </svg>
                <h3 class="text-xl font-bold mb-2">Global Access</h3>
                <p class="text-gray-400">Connect from anywhere very easily.</p>
            </div>
        </div>
    </div>
</body>

</html>
