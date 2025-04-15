<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Lingo - User</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="{{ asset('images/logo.svg') }}">
</head>

<body class="bg-gray-900">
    {{-- Success message --}}
    @if (Session::has('success'))
        <div class="fixed top-4 right-4 z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-sm w-full">
                <div class="p-4 flex items-center border-b border-gray-200 dark:border-gray-700">
                    <img src="{{ asset('images/logo.svg') }}" class="w-5 h-5 mr-2 rounded" alt="Logo">
                    <strong class="text-gray-900 dark:text-white font-medium flex-grow">Live Lingo</strong>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Just Now</span>
                    <button type="button" class="ml-4 text-gray-400 hover:text-gray-500 focus:outline-none"
                        onclick="this.parentElement.parentElement.parentElement.remove()">
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <div class="p-4 bg-green-50 dark:bg-green-900/50 rounded-b-lg">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <p class="ml-3 text-sm font-medium text-green-800 dark:text-green-200">
                            {{ Session::get('success') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Error message --}}
    @if (Session::has('error'))
        <div class="fixed top-4 right-4 z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-sm w-full">
                <div class="p-4 flex items-center border-b border-gray-200 dark:border-gray-700">
                    <img src="{{ asset('images/logo.svg') }}" class="w-5 h-5 mr-2 rounded" alt="Logo">
                    <strong class="text-gray-900 dark:text-white font-medium flex-grow">Live Lingo</strong>
                    <span class="text-sm text-gray-500 dark:text-gray-400">Just Now</span>
                    <button type="button" class="ml-4 text-gray-400 hover:text-gray-500 focus:outline-none"
                        onclick="this.parentElement.parentElement.parentElement.remove()">
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <div class="p-4 bg-red-50 dark:bg-red-900/50 rounded-b-lg">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        <p class="ml-3 text-sm font-medium text-red-800 dark:text-red-200">
                            {{ Session::get('error') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @yield('content')
</body>

</html>
