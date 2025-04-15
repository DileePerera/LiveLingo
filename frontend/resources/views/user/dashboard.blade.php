@extends('layouts.user')

@section('content')
    <div class="min-h-screen bg-gray-900 flex">
        <!-- Sidebar -->
        <div class="w-64 bg-gray-800 p-4">
            <div class="flex items-center space-x-2 mb-8">
                <svg class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 10l4.553 2.276A2 2 0 0120 14.118v3.764a2 2 0 01-1.447 1.842L15 20m-6 0l-3.553-1.276A2 2 0 014 17.882v-3.764a2 2 0 011.447-1.842L9 10m6 0V6a2 2 0 00-2-2H9a2 2 0 00-2 2v4m8 0H7" />
                </svg>
                <span class="text-xl font-bold text-white">Live Lingo</span>
            </div>

            <div class="space-y-6">
                <div class="flex flex-col items-center p-4 bg-gray-700 rounded-lg">
                    <svg class="h-20 w-20 text-gray-300 mb-2" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 14l-9-5 9-5 9 5-9 5zm0 7v-6m0 6H5m7 0h7" />
                    </svg>
                    <h3 class="text-white font-medium">{{ session('user')->name }}</h3>
                    <p class="text-gray-400 text-sm">Premium User</p>
                </div>

                <nav class="space-y-2">
                    <a href="#"
                        class="block text-gray-300 hover:text-white p-2 rounded hover:bg-gray-700 flex items-center">
                        <span>📅 Schedule</span>
                    </a>
                    <a href="#"
                        class="block text-gray-300 hover:text-white p-2 rounded hover:bg-gray-700 flex items-center">
                        <span>⏳ History</span>
                    </a>
                    <a href="#"
                        class="block text-gray-300 hover:text-white p-2 rounded hover:bg-gray-700 flex items-center">
                        <span>⚙️ Settings</span>
                    </a>
                </nav>
            </div>

            <a href="{{ route('logout') }}" class="absolute bottom-4 left-4 text-gray-300 hover:text-white">
                <span>🚪 Sign Out</span>
            </a>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <!-- Quick Actions -->
            <div class="grid grid-cols-2 gap-4 mb-8">
                <a href="{{ route('meeting.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 h-24 flex items-center justify-center text-lg text-white rounded-lg">
                    ➕ Create New Meeting
                </a>
                <a href="#"
                    class="bg-yellow-600 hover:bg-yellow-700 h-24 flex items-center justify-center text-lg text-white rounded-lg">
                    🔗 Join Meeting
                </a>
            </div>

            <div class="grid grid-cols-2 gap-8">
                <!-- Upcoming Meetings -->
                <div class="bg-gray-800 rounded-lg p-6">
                    <h2 class="text-xl font-bold text-white mb-4">📅 Upcoming Meetings</h2>
                    <div class="space-y-4">
                        @foreach ($meetings as $meeting)
                            <div class="bg-gray-700 p-4 rounded-lg">
                                <div class="flex justify-between">
                                    <div>
                                        <h3 class="text-white font-medium">{{ $meeting['description'] }}</h3>
                                        <p class="text-gray-400 text-sm">🕒 {{ $meeting['start_time'] }}</p>
                                    </div>
                                    <p class="text-gray-400">👥 {{ $meeting['start_date'] }}</p>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    <a href="/meeting/{{ $meeting['meeting_link'] }}" 
                                        class="w-full sm:w-5/8 mt-3 text-center bg-blue-600 hover:bg-blue-700 p-2 rounded-lg text-white">Join
                                        Meeting</a>
                                    <a href="#" id="copyLinkButton" data-meeting-link="{{ $meeting['meeting_link'] }}"
                                        class="w-full sm:w-2/8 mt-3 text-center bg-green-600 hover:bg-green-700 p-2 rounded-lg text-white copyLinkButton">Copy
                                        Link</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Meetings -->
                <div class="bg-gray-800 rounded-lg p-6">
                    <h2 class="text-xl font-bold text-white mb-4">⏳ Recent Meetings</h2>
                    <div class="space-y-4">
                        {{-- @foreach ($recentMeetings as $meeting)
                        <div class="bg-gray-700 p-4 rounded-lg">
                            <div class="flex justify-between">
                                <div>
                                    <h3 class="text-white font-medium">{{ $meeting['title'] }}</h3>
                                    <p class="text-gray-400 text-sm">📆 {{ $meeting['date'] }}</p>
                                </div>
                                <p class="text-gray-400">⏳ {{ $meeting['duration'] }}</p>
                            </div>
                            <div class="flex space-x-2 mt-3">
                                <a href="#" class="flex-1 text-center border border-gray-400 p-2 rounded-lg text-white">View Recording</a>
                                <a href="#" class="flex-1 text-center border border-gray-400 p-2 rounded-lg text-white">Meeting Notes</a>
                            </div>
                        </div>
                    @endforeach --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden message box -->
    <div id="copyMessage" class="fixed bottom-4 right-4 bg-green-600 text-white p-4 rounded-lg shadow-lg hidden">
        Link copied to clipboard!
    </div>


    <script>
        document.querySelectorAll(".copyLinkButton").forEach(button => {
            button.addEventListener("click", function(event) {
                event.preventDefault();

                const appUrl = "{{ url('/') }}";
                const meetingLink = button.getAttribute(
                    "data-meeting-link");
                const fullLink = appUrl + '/meeting/' + meetingLink;

                const textarea = document.createElement("textarea");
                textarea.value = fullLink;
                document.body.appendChild(textarea);

                textarea.select();
                document.execCommand("copy");

                document.body.removeChild(textarea);

                // Show success message
                const messageBox = document.getElementById("copyMessage");
                messageBox.classList.remove("hidden"); // Show the message box

                // Hide the message box after 3 seconds
                setTimeout(() => {
                    messageBox.classList.add("hidden"); // Hide the message box again
                }, 3000);
            });
        });
    </script>
@endsection
