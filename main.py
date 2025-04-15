from flask import Flask, request
from flask_socketio import SocketIO, emit
import pyaudio
import numpy as np
from vosk import Model, KaldiRecognizer
import json
from src.En_Si.models.positional import PositionalEmbedding as PositionalEmbedding_si
from src.En_Ta.models.positional import PositionalEmbedding as PositionalEmbedding_ta
from src.En_Si.models.encoder import TransformerEncoder as TransformerEncoder_si
from src.En_Ta.models.encoder import TransformerEncoder as TransformerEncoder_ta
from src.En_Si.models.decoder import TransformerDecoder as TransformerDecoder_si
from src.En_Ta.models.decoder import TransformerDecoder as TransformerDecoder_ta
from src.En_Si.data.vectorize import source_vectorization, target_vectorization
from src.En_Ta.data.vectorize import source_vectorization as source_vectorization_ta
from src.En_Ta.data.vectorize import target_vectorization as target_vectorization_ta
from tensorflow import keras
import threading
import logging
import time

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

app = Flask(__name__)
socketio = SocketIO(app, cors_allowed_origins="*", logger=True, engineio_logger=True)

# Load Vosk Model
vosk_model = Model("vosk-model-en-us-0.22") 
recognizer = KaldiRecognizer(vosk_model, 16000)

# Load Sinhala Model
sinhala_vocab = target_vectorization.get_vocabulary()
sinhala_index_lookup = dict(zip(range(len(sinhala_vocab)), sinhala_vocab))

transformer_si = keras.models.load_model(
    "model/transformer_model.h5",
    custom_objects={
        "PositionalEmbedding": PositionalEmbedding_si,
        "TransformerEncoder": TransformerEncoder_si,
        "TransformerDecoder": TransformerDecoder_si
    },
    compile=False
)

# Load Tamil Model
tamil_vocab = target_vectorization_ta.get_vocabulary()
tamil_index_lookup = dict(zip(range(len(tamil_vocab)), tamil_vocab))

transformer_ta = keras.models.load_model(
    "model/transformer_model_ta.h5",
    custom_objects={
        "PositionalEmbedding": PositionalEmbedding_ta,
        "TransformerEncoder": TransformerEncoder_ta,
        "TransformerDecoder": TransformerDecoder_ta
    },
    compile=False
)

max_decoded_sentence_length = 20

def decode_sequence(input_sentence, transformer, target_vectorization, vocab_lookup):
    """Decode sequence for translation."""
    tokenized_input = source_vectorization([input_sentence])
    decoded_sentence = "[start]"

    for i in range(max_decoded_sentence_length):
        tokenized_target = target_vectorization([decoded_sentence])[:, :-1]
        predictions = transformer([tokenized_input, tokenized_target])
        sampled_token_index = np.argmax(predictions[0, i, :])

        if sampled_token_index >= len(vocab_lookup) or vocab_lookup[sampled_token_index] == "[end]":
            break
        
        sampled_token = vocab_lookup.get(sampled_token_index, "[UNK]")
        decoded_sentence += " " + sampled_token
        yield sampled_token

def decode_sequence_tamil(input_sentence):
    return decode_sequence(input_sentence, transformer_ta, target_vectorization_ta, tamil_index_lookup)

def decode_sequence_sinhala(input_sentence):
    return decode_sequence(input_sentence, transformer_si, target_vectorization, sinhala_index_lookup)

def audio_processing(callback, stop_event):
    """Process audio using Vosk."""
    p = pyaudio.PyAudio()
    stream = p.open(format=pyaudio.paInt16, channels=1, rate=16000, input=True, frames_per_buffer=4096)

    print("\n=== AUDIO PROCESSING STARTED ===")
    print("Listening for English speech... (Ctrl+C to stop)\n")
    
    try:
        while not stop_event.is_set():
            data = stream.read(4096)
            if recognizer.AcceptWaveform(data):
                result = json.loads(recognizer.Result())
                transcript = result.get("text", "").strip()
                if transcript:
                    print(f"[ENGLISH SPEECH] {transcript}")
                    callback(transcript)
    except KeyboardInterrupt:
        print("\nStopping transcription...")
    finally:
        stream.stop_stream()
        stream.close()
        p.terminate()
        print("Audio resources released")

@socketio.on('start_transcription')
def handle_start_recording():
    print(f"\nStarting transcription for {request.sid}")
    
    sid = request.sid
    stop_event = threading.Event()

    def process_audio(transcript):
        print(f"🎤 RAW: {transcript}")

        socketio.emit('transcription', {'text': transcript, 'type': 'original'}, room=sid)

        print("🔄 Translating to Sinhala...")
        sinhala_translation = list(decode_sequence_sinhala(transcript))
        socketio.emit('transcription', {'text': ' '.join(sinhala_translation), 'type': 'sinhala'}, room=sid)

        print("🔄 Translating to Tamil...")
        tamil_translation = list(decode_sequence_tamil(transcript))
        socketio.emit('transcription', {'text': ' '.join(tamil_translation), 'type': 'tamil'}, room=sid)

    threading.Thread(target=audio_processing, args=(process_audio, stop_event), daemon=True).start()

@app.route('/test_audio')
def test_audio():
    """Test audio capture independently."""
    p = pyaudio.PyAudio()
    stream = p.open(format=pyaudio.paInt16, channels=1, rate=16000, input=True, frames_per_buffer=4096)
    
    print("\nSpeak now (5 second recording)...")
    frames = [stream.read(4096) for _ in range(int(16000 / 4096 * 5))]
    
    stream.stop_stream()
    stream.close()
    p.terminate()
    
    audio_data = np.frombuffer(b''.join(frames), dtype=np.int16)
    print(f"Captured {len(audio_data)} samples")
    return "Audio captured - check terminal"

if __name__ == "__main__":
    print("=== SERVER STARTING ===")
    print("WebSocket endpoint: ws://localhost:5000")
    print("Waiting for client connections...\n")
    socketio.run(app, host='0.0.0.0', port=5000)
