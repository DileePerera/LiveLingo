@extends('layouts.auth')

@section('content')
    <div class="min-h-screen bg-gray-900 text-gray-100 flex items-center justify-center">
        <div class="w-full max-w-md p-8 bg-gray-800 rounded-lg shadow-xl">
            <!-- Logo -->
            <div class="flex items-center justify-center mb-8">
                <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M15 10l4.35-4.35a2 2 0 10-2.83-2.83L12 7.17 7.47 3.12a2 2 0 00-2.83 2.83L9 10"></path>
                </svg>
                <span class="text-2xl font-bold ml-2">Live Lingo</span>
            </div>

            <!-- Title -->
            <h2 class="text-2xl font-bold text-center mb-8">Welcome Back</h2>

            <!-- Form -->
            <form method="POST" action="{{ route('log') }}" class="space-y-6">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-2">Email</label>
                    <div class="relative">
                        <input type="email" name="email" required
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg py-2 pl-4 pr-4 focus:outline-none focus:border-blue-500"
                            placeholder="Enter your email">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-2">Password</label>
                    <div class="relative">
                        <input type="password" name="password" required
                            class="w-full bg-gray-700 border border-gray-600 rounded-lg py-2 pl-4 pr-4 focus:outline-none focus:border-blue-500"
                            placeholder="Enter your password">
                    </div>
                </div>

                <div class="text-right">
                    <a href="#" class="text-sm text-blue-500 hover:text-blue-400">Forgot password?</a>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 py-2 rounded-lg">
                    Sign In
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-gray-400">
                    Don't have an account?
                    <a href="{{ route('reg_index') }}" class="ml-2 text-blue-500 hover:text-blue-400">Sign up</a>
                </p>
            </div>
        </div>
    </div>
@endsection
