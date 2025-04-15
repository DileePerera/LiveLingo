@extends('layouts.user')

@section('content')
<div class="bg-gray-900 min-h-screen">
    <!-- Meeting Header -->
    <div class="bg-gray-800 p-4 shadow-md">
        <div class="flex justify-between items-center">
            <div class="flex items-center">
                <h1 class="text-white text-xl font-semibold">{{ $meeting->title ?? 'Video Conference' }}</h1>
                <span class="ml-3 bg-red-500 text-white text-xs px-2 py-1 rounded-full flex items-center">
                    <span class="animate-pulse w-2 h-2 bg-white rounded-full mr-1"></span>
                    LIVE
                </span>
            </div>
            <div class="text-white text-sm">
                <span class="mr-2" id="meeting-time">00:00:00</span>
                <span id="meeting-id">{{ $meeting->meeting_link }}</span>
            </div>
        </div>
    </div>

    <!-- Main Meeting Area -->
    <div class="px-4 py-6">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
            <!-- Video Grid -->
            <div class="lg:col-span-3">
                <div class="bg-gray-800 rounded-lg p-2">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2" id="video-grid">
                        <!-- Local Video -->
                        <div class="bg-gray-700 rounded-lg relative overflow-hidden h-64 flex items-center justify-center"
                            id="local-video-container">
                            <video id="local-video" autoplay muted class="w-full h-full object-cover"></video>
                            <div class="absolute bottom-2 left-2 bg-gray-900 bg-opacity-60 px-2 py-1 rounded text-white text-sm">
                                You
                            </div>
                            <div class="absolute top-0 left-0 w-full h-full flex items-center justify-center camera-off hidden">
                                <div class="bg-gray-800 rounded-full p-4">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subtitles and Language Selection -->
            <div class="lg:col-span-1">
                <div class="bg-gray-800 rounded-lg h-full">
                    <div class="flex border-b border-gray-700">
                        <button class="w-1/2 py-3 text-white font-medium focus:outline-none text-center tab-btn active" data-tab="subtitles-tab">Transcription</button>
                        <button class="w-1/2 py-3 text-white font-medium focus:outline-none text-center tab-btn" data-tab="language-tab">Language</button>
                    </div>

                    <!-- Subtitles Tab -->
                    <div class="tab-content block" id="subtitles-tab">
                        <div class="p-4 h-96 overflow-y-auto" id="subtitle-display">
                            <div class="subtitle-container">
                                <p class="text-gray-400 text-center text-sm py-2">Real-time transcription will appear here</p>
                            </div>
                        </div>
                        <div class="p-4 border-t border-gray-700">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <span id="detected-language" class="text-blue-400 text-sm mr-2"></span>
                                    <span id="translation-status" class="text-green-400 text-sm"></span>
                                    <button id="subtitle-toggle" class="bg-gray-700 text-white px-3 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 mr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm3 1a1 1 0 000 2h4a1 1 0 100-2H5zm0 3a1 1 0 000 2h4a1 1 0 100-2H5zm5-3a1 1 0 000 2h2a1 1 0 100-2h-2zm0 3a1 1 0 000 2h2a1 1 0 100-2h-2z" />
                                        </svg>
                                    </button>
                                    <span id="subtitle-status" class="text-green-400 text-sm">On</span>
                                </div>
                                <div>
                                    <button id="subtitle-settings" class="bg-gray-700 text-white px-3 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Language Selection Tab -->
                    <div class="tab-content hidden" id="language-tab">
                        <div class="p-4 h-96 overflow-y-auto">
                            <div class="mb-4">
                                <label for="subtitle-language" class="block text-white text-sm font-medium mb-2">Subtitle Language</label>
                                <select id="subtitle-language" class="w-full bg-gray-700 text-white rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="en">English</option>
                                    <option value="si">Sinhala</option>
                                    <option value="ta">Tamil</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="translation-language" class="block text-white text-sm font-medium mb-2">Translation Language</label>
                                <select id="translation-language" class="w-full bg-gray-700 text-white rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="none">No Translation</option>
                                    <option value="en">English</option>
                                    <option value="si">Sinhala</option>
                                    <option value="ta">Tamil</option>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="font-size" class="block text-white text-sm font-medium mb-2">Font Size</label>
                                <select id="font-size" class="w-full bg-gray-700 text-white rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="sm">Small</option>
                                    <option value="md" selected>Medium</option>
                                    <option value="lg">Large</option>
                                    <option value="xl">Extra Large</option>
                                </select>
                            </div>

                            <div class="flex items-center mb-4">
                                <input type="checkbox" id="auto-scroll" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-700 rounded">
                                <label for="auto-scroll" class="ml-2 block text-white text-sm">Auto-scroll subtitles</label>
                            </div>

                            <button id="apply-language-settings" class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Apply Settings
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Meeting Controls -->
        <div class="fixed bottom-0 left-0 right-0 bg-gray-800 p-4">
            <div class="flex justify-center items-center space-x-4">
                <button id="mic-btn" class="bg-gray-700 p-3 rounded-full hover:bg-gray-600 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                    </svg>
                </button>
                <button id="camera-btn" class="bg-gray-700 p-3 rounded-full hover:bg-gray-600 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </button>
                <button id="screen-share-btn" class="bg-gray-700 p-3 rounded-full hover:bg-gray-600 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </button>
                <button id="invite-btn" class="bg-gray-700 p-3 rounded-full hover:bg-gray-600 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </button>
                <button id="end-call-btn" class="bg-red-600 p-3 rounded-full hover:bg-red-700 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 8l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M5 3a2 2 0 00-2 2v1c0 8.284 6.716 15 15 15h1a2 2 0 002-2v-3.28a1 1 0 00-.684-.948l-4.493-1.498a1 1 0 00-1.21.502l-1.13 2.257a11.042 11.042 0 01-5.516-5.517l2.257-1.128a1 1 0 00.502-1.21L9.228 3.683A1 1 0 008.279 3H5z" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Copy Meeting Link Modal -->
        <div id="invite-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-gray-800 rounded-lg p-6 max-w-md w-full">
                <h3 class="text-white text-xl font-semibold mb-4">Invite People</h3>
                <div class="mb-4">
                    <label class="block text-gray-300 text-sm font-medium mb-2">Meeting Link</label>
                    <div class="flex">
                        <input type="text" id="meeting-link-input"
                            value="{{ url('meeting/' . $meeting->meeting_link) }}"
                            class="w-full bg-gray-700 text-white rounded-l-md px-4 py-2 focus:outline-none" readonly>
                        <button id="copy-link-btn"
                            class="bg-blue-600 text-white px-4 py-2 rounded-r-md hover:bg-blue-700 focus:outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" />
                                <path
                                    d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button id="close-modal-btn"
                        class="bg-gray-700 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:outline-none">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for Video Conference -->
    <!-- Add Socket.IO client -->
    <script src="https://cdn.socket.io/4.5.4/socket.io.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // WebSocket Configuration
        const socket = io('http://localhost:5000', {
            withCredentials: true,
            transports: ['websocket'],
            reconnection: true,
            reconnectionAttempts: 5,
            reconnectionDelay: 3000
        });
        const subtitleDisplay = document.getElementById('subtitle-display');

        // State Management
        let mediaState = {
            localStream: null,
            micActive: true,
            cameraActive: true,
            screenShareActive: false,
            subtitlesEnabled: true,
            currentSubtitleLang: 'en',
            currentTranslationLang: 'none',
            autoScroll: true
        };

        // Transcription Buffer
        // let transcriptionBuffer = {
        //     original: '',
        //     translated: ''
        // };
        // let lastUpdate = Date.now();

        // WebSocket Events
        socket.on('connect', () => {
            console.log('Connected to transcription server');
            startTranscription();
            addSystemSubtitle('Connected to transcription service');
        });

        

        socket.on('disconnect', () => {
            addSystemSubtitle('Connection to transcription service lost');
        });

        socket.on('connect_error', (error) => {
            console.error('Connection error:', error);
            addSystemSubtitle('Connection error: Trying to reconnect...');
        });

        // Media Initialization
        async function initializeMedia() {
            try {
                const constraints = {
                    video: { width: 1280, height: 720, frameRate: 30 },
                    audio: { echoCancellation: true, noiseSuppression: true }
                };

                mediaState.localStream = await navigator.mediaDevices.getUserMedia(constraints);
                document.getElementById('local-video').srcObject = mediaState.localStream;

                // Audio visualization
                const audioContext = new AudioContext();
                const analyser = audioContext.createAnalyser();
                const microphone = audioContext.createMediaStreamSource(mediaState.localStream);
                microphone.connect(analyser);

                setInterval(() => {
                    const dataArray = new Uint8Array(analyser.frequencyBinCount);
                    analyser.getByteFrequencyData(dataArray);
                    const average = dataArray.reduce((a, b) => a + b) / dataArray.length;
                    document.getElementById('mic-btn').style.transform = `scale(${1 + average/100})`;
                }, 100);

            } catch (error) {
                console.error('Media error:', error);
                addSystemSubtitle('Error: Media access denied');
                mediaState.localStream = await navigator.mediaDevices.getUserMedia({ audio: true });
            }
        }

        // // Subtitle Management
        // function updateSubtitles() {
        //     if (transcriptionBuffer.original) {
        //         addSubtitle('Speaker', transcriptionBuffer.original.trim(), 'original');
        //     }
        //     if (transcriptionBuffer.translated) {
        //         addSubtitle('System', transcriptionBuffer.translated.trim(), 'translated');
        //     }
            
        //     transcriptionBuffer = {
        //         original: transcriptionBuffer.original.split(/[.!?]/).pop() || '',
        //         translated: transcriptionBuffer.translated.split(/[.!?]/).pop() || ''
        //     };
        // }

        // function addSubtitle(speaker, text, type) {
        //     if (!mediaState.subtitlesEnabled) return;

        //     const subtitleEl = document.createElement('div');
        //     subtitleEl.classList.add('mb-2', 'px-2', 'py-1', 'rounded');

        //     if (type === 'original') {
        //         subtitleEl.classList.add('bg-gray-700');
        //         subtitleEl.innerHTML = `
        //             <div class="text-blue-300 text-xs mb-1">${speaker} (${mediaState.currentSubtitleLang.toUpperCase()})</div>
        //             <div class="text-white text-sm subtitle-text">${text}</div>
        //         `;
        //     } else {
        //         subtitleEl.classList.add('bg-green-900');
        //         subtitleEl.innerHTML = `
        //             <div class="text-green-300 text-xs mb-1">Translation (${mediaState.currentTranslationLang.toUpperCase()})</div>
        //             <div class="text-white text-sm subtitle-text">${text}</div>
        //         `;
        //     }

        //     const display = document.getElementById('subtitle-display');
        //     display.appendChild(subtitleEl);

        //     if (mediaState.autoScroll) {
        //         display.scrollTop = display.scrollHeight;
        //     }
        // }

        // Initialize Application
        initializeMedia().catch(error => {
            console.error('Initialization failed:', error);
            addSystemSubtitle('Failed to initialize media devices');
        });

        // Cleanup
        window.addEventListener('beforeunload', () => {
            if (mediaState.localStream) {
                mediaState.localStream.getTracks().forEach(track => track.stop());
            }
            socket.disconnect();
        });




            // Variables
            let localStream;
            let peers = {};
            const videoGrid = document.getElementById('video-grid');
            const localVideo = document.getElementById('local-video');
            const micBtn = document.getElementById('mic-btn');
            const cameraBtn = document.getElementById('camera-btn');
            const screenShareBtn = document.getElementById('screen-share-btn');
            const inviteBtn = document.getElementById('invite-btn');
            const endCallBtn = document.getElementById('end-call-btn');
            const copyLinkBtn = document.getElementById('copy-link-btn');
            const closeModalBtn = document.getElementById('close-modal-btn');
            const inviteModal = document.getElementById('invite-modal');
            const meetingLinkInput = document.getElementById('meeting-link-input');
            const meetingTime = document.getElementById('meeting-time');
            const tabBtns = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');

            // Subtitle Variables
            const subtitleToggle = document.getElementById('subtitle-toggle');
            const subtitleStatus = document.getElementById('subtitle-status');
            // const subtitleDisplay = document.getElementById('subtitle-display');
            const subtitleLanguage = document.getElementById('subtitle-language');
            const translationLanguage = document.getElementById('translation-language');
            const fontSize = document.getElementById('font-size');
            const autoScroll = document.getElementById('auto-scroll');
            const applyLanguageSettings = document.getElementById('apply-language-settings');
            const subtitleSettings = document.getElementById('subtitle-settings');

            let micActive = true;
            let cameraActive = true;
            let screenShareActive = false;
            let startTime = new Date();

            let subtitlesEnabled = true;
            let currentSubtitleLanguage = 'en';
            let currentTranslationLanguage = 'none';
            let currentFontSize = 'md';
            let autoScrollEnabled = true;


            // socket.on('transcription', (data) => {
            //     if (data.original) {
            //         currentSentence += data.original + ' ';
            //         addSubtitle('Speaker', currentSentence, 'original');
            //     }
                
            //     if (data.translated) {
            //         currentTranslation += data.translated + ' ';
            //         addSubtitle('System', currentTranslation, 'translated');
            //     }

            //     // Simple sentence boundary detection
            //     if (data.original && /[.!?]$/.test(data.original)) {
            //         currentSentence = '';
            //         currentTranslation = '';
            //     }
            // });

            socket.on('disconnect', () => {
                console.log('Disconnected from transcription server');
                addSystemSubtitle('Connection to transcription service lost');
            });
            let isTranscribing = false;
            function startTranscription() {
                if (isTranscribing) return;
                console.log("🎙️ Starting transcription...");
                socket.emit("start_transcription");
                }
            
            function stopTranscription() {
                isTranscribing = false;
                console.log("🛑 Stopped transcription.");
            }

            // Set up tabs
            tabBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    tabBtns.forEach(b => b.classList.remove('active'));
                    btn.classList.add('active');

                    const tabId = btn.getAttribute('data-tab');
                    tabContents.forEach(content => {
                        content.classList.add('hidden');
                    });
                    document.getElementById(tabId).classList.remove('hidden');
                });
            });

            // Update meeting timer
            setInterval(() => {
                const currentTime = new Date();
                const diff = new Date(currentTime - startTime);
                const hours = diff.getUTCHours().toString().padStart(2, '0');
                const minutes = diff.getUTCMinutes().toString().padStart(2, '0');
                const seconds = diff.getUTCSeconds().toString().padStart(2, '0');
                meetingTime.textContent = `${hours}:${minutes}:${seconds}`;
            }, 1000);

            // Initialize media devices
            async function initializeMedia() {
                try {
                    localStream = await navigator.mediaDevices.getUserMedia({
                        video: true,
                        audio: true
                    });

                    localVideo.srcObject = localStream;

                    // Here you would normally initialize your WebRTC connection
                    // This is a placeholder for your actual WebRTC implementation
                    console.log('Media initialized');

                    // Add system message to subtitles
                    addSystemSubtitle('Your camera and microphone are now active');
                } catch (error) {
                    console.error('Error accessing media devices:', error);
                    addSystemSubtitle('Error: Could not access camera or microphone');
                }
            }

            // Toggle microphone
            micBtn.addEventListener('click', () => {
                if (localStream) {
                    const audioTracks = localStream.getAudioTracks();
                    if (audioTracks.length > 0) {
                        micActive = !micActive;
                        audioTracks[0].enabled = micActive;

                        if (micActive) {
                            micBtn.classList.remove('bg-red-600');
                            micBtn.classList.add('bg-gray-700');
                            addSystemSubtitle('Microphone unmuted');
                        } else {
                            micBtn.classList.remove('bg-gray-700');
                            micBtn.classList.add('bg-red-600');
                            addSystemSubtitle('Microphone muted');
                        }
                    }
                }
            });

            // Toggle camera
            cameraBtn.addEventListener('click', () => {
                if (localStream) {
                    const videoTracks = localStream.getVideoTracks();
                    if (videoTracks.length > 0) {
                        cameraActive = !cameraActive;
                        videoTracks[0].enabled = cameraActive;

                        const cameraOffDisplay = document.querySelector('.camera-off');

                        if (cameraActive) {
                            cameraBtn.classList.remove('bg-red-600');
                            cameraBtn.classList.add('bg-gray-700');
                            cameraOffDisplay.classList.add('hidden');
                            addSystemSubtitle('Camera turned on');
                        } else {
                            cameraBtn.classList.remove('bg-gray-700');
                            cameraBtn.classList.add('bg-red-600');
                            cameraOffDisplay.classList.remove('hidden');
                            addSystemSubtitle('Camera turned off');
                        }
                    }
                }
            });

            // Screen sharing
            screenShareBtn.addEventListener('click', async () => {
                if (screenShareActive) {
                    // Stop screen sharing
                    if (localStream) {
                        const videoTracks = localStream.getVideoTracks();
                        if (videoTracks.length > 0) {
                            videoTracks.forEach(track => track.stop());
                        }
                    }

                    // Get camera stream again
                    try {
                        localStream = await navigator.mediaDevices.getUserMedia({
                            video: true,
                            audio: true
                        });
                        localVideo.srcObject = localStream;
                        screenShareActive = false;
                        screenShareBtn.classList.remove('bg-green-600');
                        screenShareBtn.classList.add('bg-gray-700');
                        addSystemSubtitle('Screen sharing stopped');
                    } catch (error) {
                        console.error('Error switching back to camera:', error);
                    }
                } else {
                    // Start screen sharing
                    try {
                        const screenStream = await navigator.mediaDevices.getDisplayMedia({
                            video: true
                        });

                        // Replace video track
                        if (localStream) {
                            const videoTracks = localStream.getVideoTracks();
                            if (videoTracks.length > 0) {
                                localStream.removeTrack(videoTracks[0]);
                            }

                            const screenTrack = screenStream.getVideoTracks()[0];
                            localStream.addTrack(screenTrack);

                            // Handle screen share stop event
                            screenTrack.onended = async () => {
                                // Get camera stream again
                                try {
                                    const cameraStream = await navigator.mediaDevices
                                        .getUserMedia({
                                            video: true
                                        });

                                    const cameraTrack = cameraStream.getVideoTracks()[0];
                                    localStream.removeTrack(screenTrack);
                                    localStream.addTrack(cameraTrack);

                                    localVideo.srcObject = localStream;
                                    screenShareActive = false;
                                    screenShareBtn.classList.remove('bg-green-600');
                                    screenShareBtn.classList.add('bg-gray-700');
                                    addSystemSubtitle('Screen sharing stopped');
                                } catch (error) {
                                    console.error('Error switching back to camera:', error);
                                }
                            };
                        } else {
                            localStream = screenStream;
                        }

                        localVideo.srcObject = localStream;
                        screenShareActive = true;
                        screenShareBtn.classList.remove('bg-gray-700');
                        screenShareBtn.classList.add('bg-green-600');
                        addSystemSubtitle('Screen sharing started');
                    } catch (error) {
                        console.error('Error sharing screen:', error);
                        addSystemSubtitle('Error: Could not share screen');
                    }
                }
            });

            // Invite modal
            inviteBtn.addEventListener('click', () => {
                inviteModal.classList.remove('hidden');
            });

            closeModalBtn.addEventListener('click', () => {
                inviteModal.classList.add('hidden');
            });

            copyLinkBtn.addEventListener('click', () => {
                meetingLinkInput.select();
                document.execCommand('copy');
                copyLinkBtn.innerHTML =
                    '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>';
                setTimeout(() => {
                    copyLinkBtn.innerHTML =
                        '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M8 3a1 1 0 011-1h2a1 1 0 110 2H9a1 1 0 01-1-1z" /><path d="M6 3a2 2 0 00-2 2v11a2 2 0 002 2h8a2 2 0 002-2V5a2 2 0 00-2-2 3 3 0 01-3 3H9a3 3 0 01-3-3z" /></svg>';
                }, 2000);
                addSystemSubtitle('Meeting link copied to clipboard');
            });

            // End call
            endCallBtn.addEventListener('click', () => {
                if (confirm('Are you sure you want to end the call?')) {
                    if (localStream) {
                        localStream.getTracks().forEach(track => track.stop());
                    }

                    // Here you would disconnect from peers and clean up WebRTC
                    window.location.href = '{{ route('dashboard') }}';
                }
            });

            // Subtitle functionality

            // Toggle subtitles on/off
            subtitleToggle.addEventListener('click', () => {
                subtitlesEnabled = !subtitlesEnabled;

                if (subtitlesEnabled) {
                    subtitleStatus.textContent = 'On';
                    subtitleStatus.classList.remove('text-red-400');
                    subtitleStatus.classList.add('text-green-400');
                    subtitleDisplay.classList.remove('hidden');
                } else {
                    subtitleStatus.textContent = 'Off';
                    subtitleStatus.classList.remove('text-green-400');
                    subtitleStatus.classList.add('text-red-400');
                    subtitleDisplay.classList.add('hidden');
                }
            });

            // Apply language and display settings
            applyLanguageSettings.addEventListener('click', () => {
                currentSubtitleLanguage = subtitleLanguage.value;
                currentTranslationLanguage = translationLanguage.value;
                currentFontSize = fontSize.value;
                autoScrollEnabled = autoScroll.checked;

                // Update subtitle display with new settings
                updateSubtitleStyles();

                // Show confirmation message
                addSystemSubtitle(
                    `Subtitle settings updated: ${currentSubtitleLanguage.toUpperCase()} ${currentTranslationLanguage !== 'none' ? '+ ' + currentTranslationLanguage.toUpperCase() : ''}`
                    );

                // In a real app, you would restart speech recognition with the new language
                console.log(
                    `Subtitle language: ${currentSubtitleLanguage}, Translation: ${currentTranslationLanguage}`
                    );
            });

            socket.on('transcription', (data) => {
                console.log('Received:', data);
                
                // Create message container
                const messageDiv = document.createElement('div');
                messageDiv.className = 'p-2 rounded-lg';
                
                if (currentTranslationLanguage === 'none') {
                    if (data.type === 'original') {
                        messageDiv.classList.add('bg-gray-700');
                        messageDiv.innerHTML = `
                            <div class="text-white">${data.text}</div>
                        `;
                }
                    
                } else if(currentTranslationLanguage === 'ta'){
                    if(data.type === 'tamil'){
                    messageDiv.classList.add('bg-green-900');
                        messageDiv.innerHTML = `
                        
                            <div class="text-white">${data.text}</div>
                        `;
                    }
                } else {
                    if(data.type === 'sinhala'){
                    messageDiv.classList.add('bg-blue-900');
                        messageDiv.innerHTML = `
                            
                            <div class="text-white">${data.text}</div>
                        `;
                    }
                }

                        
                    // Add to display and auto-scroll
                    subtitleDisplay.appendChild(messageDiv);
                subtitleDisplay.scrollTop = subtitleDisplay.scrollHeight;
            });

            // Update subtitle display styles based on settings
            function updateSubtitleStyles() {
                const subtitles = subtitleDisplay.querySelectorAll('.subtitle-text');

                subtitles.forEach(subtitle => {
                    // Remove all font size classes
                    subtitle.classList.remove('text-sm', 'text-base', 'text-lg', 'text-xl');

                    // Add selected font size class
                    switch (currentFontSize) {
                        case 'sm':
                            subtitle.classList.add('text-sm');
                            break;
                        case 'md':
                            subtitle.classList.add('text-base');
                            break;
                        case 'lg':
                            subtitle.classList.add('text-lg');
                            break;
                        case 'xl':
                            subtitle.classList.add('text-xl');
                            break;
                    }
                });
            }

            // Set up dummy subtitles (in a real app, these would come from speech-to-text API)
            // let dummySubtitles = [{
            //         startTime: 2000,
            //         text: "Hello everyone and welcome to the meeting.",
            //         speaker: "You"
            //     },
            //     {
            //         startTime: 5000,
            //         text: "Today we'll be discussing the new project timeline.",
            //         speaker: "You"
            //     },
            //     {
            //         startTime: 10000,
            //         text: "Let's start by reviewing our progress from last week.",
            //         speaker: "You"
            //     }
            // ];

            // Simulate receiving subtitles (in a real app, this would come from a speech recognition API)
            function simulateSubtitles() {
                dummySubtitles.forEach((subtitle, index) => {
                    setTimeout(() => {
                        if (subtitlesEnabled) {
                            addSubtitle(subtitle.speaker, subtitle.text);
                        }
                    }, subtitle.startTime);
                });
            }

            // Add subtitle to display
            function addSubtitle(speaker, text, type) {
                if (!subtitlesEnabled) return;
                const subtitleEl = document.createElement('div');
                subtitleEl.classList.add('mb-2', 'px-2', 'py-1', 'rounded');

                // const speakerEl = document.createElement('div');
                // speakerEl.classList.add('text-sm', 'font-semibold', 'text-blue-400');
                // speakerEl.textContent = speaker;

                // const contentEl = document.createElement('div');
                // contentEl.classList.add('subtitle-text', 'bg-gray-700', 'rounded', 'p-2', 'text-white');

                // // Apply font size
                // switch (currentFontSize) {
                //     case 'sm':
                //         contentEl.classList.add('text-sm');
                //         break;
                //     case 'md':
                //         contentEl.classList.add('text-base');
                //         break;
                //     case 'lg':
                //         contentEl.classList.add('text-lg');
                //         break;
                //     case 'xl':
                //         contentEl.classList.add('text-xl');
                //         break;
                // }

                // contentEl.textContent = text;

                // // Add translation if enabled
                // if (currentTranslationLanguage !== 'none') {
                //     const translationEl = document.createElement('div');
                //     translationEl.classList.add('mt-1', 'text-gray-300', 'italic', 'text-sm');

                //     // In a real app, you would call a translation API here
                //     // This is just a placeholder
                //     translationEl.textContent = `[${currentTranslationLanguage.toUpperCase()}] ${text}`;

                //     contentEl.appendChild(translationEl);
                // }

                if (type === 'original') {
                    subtitleEl.classList.add('bg-gray-700');
                    subtitleEl.innerHTML = `
                        <div class="text-blue-300 text-xs mb-1">${speaker} (${currentSubtitleLanguage.toUpperCase()})</div>
                        <div class="text-white text-sm">${text}</div>
                    `;
                } else {
                    subtitleEl.classList.add('bg-green-900');
                    subtitleEl.innerHTML = `
                        <div class="text-green-300 text-xs mb-1">Translation (${currentTranslationLanguage.toUpperCase()})</div>
                        <div class="text-white text-sm">${text}</div>
                    `;
                }


                subtitleDisplay.appendChild(subtitleEl);

                if (autoScrollEnabled) {
                    subtitleDisplay.scrollTop = subtitleDisplay.scrollHeight;
                }
            }

            // Add system subtitle message
            function addSystemSubtitle(message) {
                const messageEl = document.createElement('div');
                messageEl.classList.add('system-message');

                const contentEl = document.createElement('p');
                contentEl.classList.add('text-gray-400', 'text-center', 'text-sm', 'py-2');
                contentEl.textContent = message;

                messageEl.appendChild(contentEl);
                subtitleDisplay.appendChild(messageEl);

                if (autoScrollEnabled) {
                    subtitleDisplay.scrollTop = subtitleDisplay.scrollHeight;
                }
            }
            // Update language selection handlers
            subtitleLanguage.addEventListener('change', () => {
                currentSubtitleLanguage = subtitleLanguage.value;
                document.getElementById('detected-language').textContent = 
                    `Detected Language: ${currentSubtitleLanguage.toUpperCase()}`;
            });

            translationLanguage.addEventListener('change', () => {
                currentTranslationLanguage = translationLanguage.value;
                document.getElementById('translation-status').textContent =
                    currentTranslationLanguage !== 'none' ? 
                    `Translating to ${currentTranslationLanguage.toUpperCase()}` : 
                    'Translation disabled';
            });
            // Initialize with default languages
            document.getElementById('detected-language').textContent = 'Detected Language: EN';
            document.getElementById('translation-status').textContent = 'Translation disabled';

            // Settings button
            subtitleSettings.addEventListener('click', () => {
                // Switch to the language tab
                const langTab = document.querySelector('[data-tab="language-tab"]');
                if (langTab) {
                    langTab.click();
                }
            });

            // Initialize the video conference
            initializeMedia();

            // Start subtitle simulation
            simulateSubtitles();

            // Click outside modal to close
            window.addEventListener('click', (e) => {
                if (e.target === inviteModal) {
                    inviteModal.classList.add('hidden');
                }
            });
        });
    </script>
@endsection

