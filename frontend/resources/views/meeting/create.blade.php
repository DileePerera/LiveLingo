@extends('layouts.user')

@section('content')
    <div class="min-h-screen bg-gray-900 p-8">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-white flex items-center gap-2">
                    <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    Create New Meeting
                </h1>
                <p class="text-gray-400 mt-2">Schedule a new video conference</p>
            </div>

            <!-- Form -->
            <div class="bg-gray-800 rounded-lg p-6 shadow-xl">
                <form action="{{ route('meeting.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Host Name -->
                    <div>
                        <label for="host_name" class="block text-sm font-medium text-gray-200 mb-2">
                            Host Name
                        </label>
                        <div class="relative">
                            <svg class="h-5 w-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <input type="hidden" value="{{ session('user')->id }}" name="host_id">
                            <input type="text" name="host_name" id="host_name" value="{{ session('user')->name }}"
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg py-2 pl-10 pr-4 text-white placeholder-gray-400 focus:outline-none focus:border-blue-500"
                                disabled placeholder="Enter host name"> 
                        </div>
                        @error('host_name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Date and Time Row -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Date -->
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-200 mb-2">
                                Start Date
                            </label>
                            <div class="relative">
                                <svg class="h-5 w-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg py-2 pl-10 pr-4 text-white focus:outline-none focus:border-blue-500">
                            </div>
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Time -->
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-200 mb-2">
                                Start Time
                            </label>
                            <div class="relative">
                                <svg class="h-5 w-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <input type="time" name="start_time" id="start_time" value="{{ old('start_time') }}"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg py-2 pl-10 pr-4 text-white focus:outline-none focus:border-blue-500">
                            </div>
                            @error('start_time')
                                <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-200 mb-2">
                            Description
                        </label>
                        <div class="relative">
                            <svg class="h-5 w-5 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <textarea name="description" id="description"
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg py-2 pl-10 pr-4 text-white placeholder-gray-400 focus:outline-none focus:border-blue-500 min-h-[100px]"
                                placeholder="Enter meeting description">{{ old('description') }}</textarea>
                        </div>
                        @error('description')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-200 mb-2">
                            Meeting Password
                        </label>
                        <div class="relative">
                            <svg class="h-5 w-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <input type="password" name="password" id="password"
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg py-2 pl-10 pr-4 text-white placeholder-gray-400 focus:outline-none focus:border-blue-500"
                                placeholder="Enter meeting password">
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-400 mt-1">Password must be at least 6 characters</p>
                    </div>

                    <!-- Buttons -->
                    <div class="flex space-x-4 pt-4">
                        <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white rounded-lg h-12">
                            Create Meeting
                        </button>
                        <a href="{{ route('dashboard') }}"
                            class="flex-1 bg-transparent border border-gray-600 text-white rounded-lg h-12 flex items-center justify-center hover:bg-gray-700">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
