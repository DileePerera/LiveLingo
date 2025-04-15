from flask import Flask, request
from flask_socketio import SocketIO, emit
import pyaudio
import numpy as np
from faster_whisper import WhisperModel
import threading
import logging
import time
import tensorflow as tf
from tensorflow import keras

# Custom Model Imports
from src.En_Si.models.positional import PositionalEmbedding as PositionalEmbedding_si
from src.En_Ta.models.positional import PositionalEmbedding as PositionalEmbedding_ta
from src.En_Si.models.encoder import TransformerEncoder as TransformerEncoder_si
from src.En_Ta.models.encoder import TransformerEncoder as TransformerEncoder_ta
from src.En_Si.models.decoder import TransformerDecoder as TransformerDecoder_si
from src.En_Ta.models.decoder import TransformerDecoder as TransformerDecoder_ta

from src.En_Si.data.vectorize import source_vectorization, target_vectorization
from src.En_Ta.data.vectorize import source_vectorization as source_vectorization_ta
from src.En_Ta.data.vectorize import target_vectorization as target_vectorization_ta

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

app = Flask(__name__)
socketio = SocketIO(app, cors_allowed_origins="*", logger=True, engineio_logger=True)

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
    """Decodes sequence for translation."""
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
    
    return decoded_sentence.split()[1:]

def audio_processing(callback, stop_event):
    """Process audio input and transcribe with Whisper."""
    model = WhisperModel("medium.en", device="cuda")
    p = pyaudio.PyAudio()
    
    logger.info("\n=== AUDIO PROCESSING STARTED ===")
    stream = p.open(format=pyaudio.paInt16, channels=1, rate=16000, input=True, frames_per_buffer=8192)
    
    try:
        while not stop_event.is_set():
            audio_data = np.frombuffer(stream.read(8192), dtype=np.int16).astype(np.float32) / 32768.0
            segments, info = model.transcribe(audio_data, beam_size=5, vad_filter=False
                                              , language="en")
            
            if info.language != "en" or info.language_probability < 0.8:
                logger.info(f"Ignored non-English speech: {info.language} ({info.language_probability:.2%})")
                continue
            
            for segment in segments:
                logger.info(f"[ENGLISH SPEECH] {segment.text}")
                callback(segment.text)
    except Exception as e:
        logger.error(f"Audio processing error: {e}")
    finally:
        stream.stop_stream()
        stream.close()
        p.terminate()
        logger.info("Audio resources released")

@socketio.on('start_transcription')
def handle_start_recording():
    sid = request.sid  # WebSocket session ID
    stop_event = threading.Event()
    
    def process_audio(transcript):
        logger.info(f"🎤 RAW: {transcript}")
        socketio.emit('transcription', {'text': transcript, 'type': 'original'}, room=sid)
        
        sinhala_translation = decode_sequence(transcript, transformer_si, target_vectorization, sinhala_index_lookup)
        socketio.emit('transcription', {'text': ' '.join(sinhala_translation), 'type': 'sinhala'}, room=sid)
        
        tamil_translation = decode_sequence(transcript, transformer_ta, target_vectorization_ta, tamil_index_lookup)
        socketio.emit('transcription', {'text': ' '.join(tamil_translation), 'type': 'tamil'}, room=sid)
    
    threading.Thread(target=audio_processing, args=(process_audio, stop_event), daemon=True).start()

@app.route('/test_audio')
def test_audio():
    """Test audio capture independently."""
    p = pyaudio.PyAudio()
    stream = p.open(format=pyaudio.paInt16, channels=1, rate=16000, input=True, frames_per_buffer=4096)
    
    logger.info("Speak now (5 second recording)...")
    frames = [stream.read(4096) for _ in range(int(16000 / 4096 * 5))]
    
    stream.stop_stream()
    stream.close()
    p.terminate()
    
    audio_data = np.frombuffer(b''.join(frames), dtype=np.int16)
    logger.info(f"Captured {len(audio_data)} samples")
    return "Audio captured - check terminal"

if __name__ == "__main__":
    logger.info("=== SERVER STARTING ===")
    logger.info("WebSocket endpoint: ws://localhost:5000")
    socketio.run(app, host='0.0.0.0', port=5000)