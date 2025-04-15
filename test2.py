import pyaudio
import numpy as np
from faster_whisper import WhisperModel
import wave
import time
import logging
# import Levenshtein  # For calculating word error rate (WER)

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

class WhisperASRTester:
    def __init__(self, model_size="medium.en", device="cuda", compute_type="float16"):
        """
        Initialize Whisper ASR tester with configurable parameters
        
        Args:
            model_size: Whisper model size (tiny, base, small, medium, large)
            device: 'cuda' or 'cpu'
            compute_type: float16 or float32
        """
        self.model = WhisperModel(model_size, device=device, compute_type=compute_type)
        self.sample_rate = 16000
        self.chunk_size = 4096
        self.vad_params = {
            'threshold': 0.5,
            'min_speech_duration_ms': 250,
            'max_speech_duration_s': 30,
            'min_silence_duration_ms': 400
        }
        
    def record_audio(self, duration=5, save_path=None):
        """Record audio from microphone and optionally save to file"""
        p = pyaudio.PyAudio()
        stream = p.open(format=pyaudio.paInt16,
                       channels=1,
                       rate=self.sample_rate,
                       input=True,
                       frames_per_buffer=self.chunk_size)
        
        logger.info(f"Recording for {duration} seconds...")
        frames = []
        
        for _ in range(0, int(self.sample_rate / self.chunk_size * duration)):
            data = stream.read(self.chunk_size)
            frames.append(data)
        
        stream.stop_stream()
        stream.close()
        p.terminate()
        
        audio_data = np.frombuffer(b''.join(frames), dtype=np.int16)
        
        if save_path:
            with wave.open(save_path, 'wb') as wf:
                wf.setnchannels(1)
                wf.setsampwidth(2)
                wf.setframerate(self.sample_rate)
                wf.writeframes(audio_data.tobytes())
            logger.info(f"Audio saved to {save_path}")
        
        return audio_data.astype(np.float32) / 32768.0  # Normalize to [-1, 1]

    def transcribe_audio(self, audio_data):
        """Transcribe audio using Whisper with VAD"""
        start_time = time.time()
        
        segments, info = self.model.transcribe(
            audio_data,
            beam_size=5,
            vad_filter=True,
            vad_parameters=self.vad_params,
            language="en"
        )
        
        transcription = " ".join([seg.text for seg in segments])
        latency = time.time() - start_time
        
        return {
            'transcription': transcription,
            'language': info.language,
            'confidence': info.language_probability,
            'latency': latency,
            'audio_duration': len(audio_data) / self.sample_rate
        }

    # def evaluate_wer(self, reference, hypothesis):
    #     """Calculate Word Error Rate (WER)"""
    #     ref_words = reference.split()
    #     hyp_words = hypothesis.split()
        
    #     if not ref_words:
    #         return 0.0 if not hyp_words else 1.0
        
    #     distance = Levenshtein.distance(reference, hypothesis)
    #     return distance / len(ref_words)

    def test_real_time(self, duration=10):
        """Real-time transcription test"""
        p = pyaudio.PyAudio()
        stream = p.open(format=pyaudio.paInt16,
                       channels=1,
                       rate=self.sample_rate,
                       input=True,
                       frames_per_buffer=self.chunk_size)
        
        logger.info(f"Starting real-time test for {duration} seconds...")
        start_time = time.time()
        
        try:
            while time.time() - start_time < duration:
                audio_chunk = np.frombuffer(stream.read(self.chunk_size), dtype=np.int16)
                audio_float = audio_chunk.astype(np.float32) / 32768.0
                
                segments, _ = self.model.transcribe(
                    audio_float,
                    vad_filter=True,
                    vad_parameters=self.vad_params
                )
                
                for seg in segments:
                    logger.info(f"Real-time: {seg.text}")
                    
        except KeyboardInterrupt:
            logger.info("Stopping real-time test...")
        finally:
            stream.stop_stream()
            stream.close()
            p.terminate()

    def benchmark(self, audio_path=None, reference_text=None, num_tests=3):
        """Run performance benchmarks"""
        results = []
        
        for i in range(num_tests):
            logger.info(f"\nRunning test {i+1}/{num_tests}")
            
            # Record or load audio
            if audio_path:
                with wave.open(audio_path, 'rb') as wf:
                    audio_data = np.frombuffer(wf.readframes(wf.getnframes()), 
                                             dtype=np.int16).astype(np.float32) / 32768.0
            else:
                audio_data = self.record_audio(duration=5)
            
            # Transcribe
            result = self.transcribe_audio(audio_data)
            
            # Evaluate if reference text provided
            if reference_text:
                result['wer'] = self.evaluate_wer(reference_text, result['transcription'])
            
            results.append(result)
            
            logger.info(f"Transcription: {result['transcription']}")
            if 'wer' in result:
                logger.info(f"Word Error Rate: {result['wer']:.2%}")
            logger.info(f"Latency: {result['latency']:.2f}s for {result['audio_duration']:.2f}s audio")
        
        return results

if __name__ == "__main__":
    # Initialize tester with medium English model
    tester = WhisperASRTester(model_size="medium.en", device="cuda")
    
    # Run benchmarks (either with pre-recorded audio or live recording)
    print("1. Test with live recording")
    print("2. Test with audio file")
    choice = input("Select option (1/2): ")
    
    if choice == "1":
        results = tester.benchmark(
            reference_text="this is a test of whisper speech recognition"
        )
    elif choice == "2":
        audio_file = input("Enter audio file path: ")
        reference = input("Enter reference transcription: ")
        results = tester.benchmark(
            audio_path=audio_file,
            reference_text=reference
        )
    
    # Print summary
    print("\n=== Benchmark Summary ===")
    avg_wer = np.mean([r.get('wer', 0) for r in results])
    avg_latency = np.mean([r['latency'] for r in results])
    print(f"Average WER: {avg_wer:.2%}")
    print(f"Average Latency: {avg_latency:.2f}s")
    
    # Run real-time test
    print("\nStarting real-time test (Ctrl+C to stop)...")
    tester.test_real_time(duration=15)