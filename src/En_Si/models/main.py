import numpy as np
import sys
import os
from end_end import transformer
from positional import PositionalEmbedding
import random
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))
from data.vectorize import source_vectorization, target_vectorization
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..','..')))
from speech_recognition.ASR import segmenting
# from end_end import transformer
from positional import PositionalEmbedding
from encoder import TransformerEncoder
from decoder import TransformerDecoder
from tensorflow import keras

sinahala_vocab = target_vectorization.get_vocabulary()
sinahala_index_lookup = dict(zip(range(len(sinahala_vocab)), sinahala_vocab))
max_decoded_sentence_length = 20

transformer = keras.models.load_model(
    "model/transformer_model.h5",
    custom_objects={"PositionalEmbedding": PositionalEmbedding, "TransformerEncoder":TransformerEncoder, "TransformerDecoder":TransformerDecoder }, 
    compile=False
)

def decode_sequence(input_sentence):
    tokenized_input_sentence = source_vectorization([input_sentence])
    decoded_sentence = "[start]"

    for i in range(max_decoded_sentence_length):
        tokenized_target_sentence = target_vectorization([decoded_sentence])[:, :-1]
        predictions = transformer([tokenized_input_sentence, tokenized_target_sentence])

        sampled_token_index = np.argmax(predictions[0, i, :])

        if sampled_token_index >= len(sinahala_vocab):
            sampled_token = "[UNK]"
        else:
            sampled_token = sinahala_index_lookup.get(sampled_token_index, "[UNK]")

        if sampled_token == "[end]":
            break
        
        decoded_sentence += " " + sampled_token
        yield sampled_token  # Yield token one by one instead of returning full sentence at once

# Callback function to handle real-time transcription
def handle_transcription(text):
    print("\nOriginal English Sentence:", text)
    print("Translated Sinhala Sentence:", end=" ")

    for token in decode_sequence(text):
        print(token, end=" ", flush=True)

# Start real-time transcription with the callback
segmenting(handle_transcription)
