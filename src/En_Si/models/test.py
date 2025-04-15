import numpy as np
import sys
import os
from positional import PositionalEmbedding
from encoder import TransformerEncoder
from decoder import TransformerDecoder
import random
from tensorflow import keras


sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))
from data.vectorize import source_vectorization, target_vectorization
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..','..')))


transformer = keras.models.load_model(
    "model/transformer_model.h5",
    custom_objects={"PositionalEmbedding": PositionalEmbedding, "TransformerEncoder":TransformerEncoder, "TransformerDecoder":TransformerDecoder }, 
    compile=False
)

# from split import test_pairs

sinahala_vocab = target_vectorization.get_vocabulary()
sinahala_index_lookup = dict(zip(range(len(sinahala_vocab)), sinahala_vocab))
max_decoded_sentence_length = 20

def decode_sequence(input_sentence):
    tokenized_input_sentence = source_vectorization([input_sentence])
    decoded_sentence = "[start]"

    for i in range(max_decoded_sentence_length):
        tokenized_target_sentence = target_vectorization([decoded_sentence])[:, :-1]
        predictions = transformer([tokenized_input_sentence, tokenized_target_sentence])
        
        sampled_token_index = np.argmax(predictions[0, i, :])

        # Ensure the token index is within valid range
        if sampled_token_index >= len(sinahala_vocab):  
            sampled_token = "[UNK]"
        else:
            sampled_token = sinahala_index_lookup.get(sampled_token_index, "[UNK]")  # Safer lookup using .get()
    # Use .get() to avoid KeyError
        
        decoded_sentence += " " + sampled_token
        if sampled_token == "[end]":
            break

    return decoded_sentence

def load_data(file_path, delimiter="\t"):
    """Loads English-Sinhala sentence pairs from a file."""
    pairs = []
    with open(file_path, "r", encoding="utf-8") as f:
        for line in f:
            parts = line.strip().split(delimiter)
            if len(parts) == 2:  # Ensure it contains both English & Sinhala
                pairs.append((parts[0], parts[1]))
    return pairs

test_pairs = load_data("reports/test.en-si")

sin_eng_texts = [pair[0] for pair in test_pairs]
for _ in range(20):
    input_sentence = random.choice(sin_eng_texts)
    print("-")
    print(input_sentence)
    print(decode_sequence(input_sentence))


# print(f"First 50 Sinhala vocab words: {sinahala_vocab[:50]}")
# print(f"Total vocab size: {len(sinahala_vocab)}")

unk_count = sinahala_vocab.count("[UNK]")
print(f"Total [UNK] tokens in vocabulary: {unk_count}")


# sentence = segmenting.text
# # for sentence in test_pairs:
# print("Original English Sentence:", sentence)
# translated_sentence = decode_sequence(sentence)
# print("Translated Sinhala Sentence:", translated_sentence)
#     # print("-" * 20)  # Separator