import numpy as np
import sys
import os
import random
from tensorflow import keras
from nltk.translate.bleu_score import sentence_bleu

from positional import PositionalEmbedding
from encoder import TransformerEncoder
from decoder import TransformerDecoder

sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))
from data.vectorize import source_vectorization, target_vectorization
sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..','..')))

# Load Transformer Model
transformer = keras.models.load_model(
    "model/transformer_model.h5",
    custom_objects={"PositionalEmbedding": PositionalEmbedding, "TransformerEncoder": TransformerEncoder, "TransformerDecoder": TransformerDecoder},
    compile=False
)

# Load vocabulary
sinahala_vocab = target_vectorization.get_vocabulary()
sinahala_index_lookup = dict(zip(range(len(sinahala_vocab)), sinahala_vocab))
max_decoded_sentence_length = 20

def decode_sequence(input_sentence):
    """Decodes an input sentence using the trained transformer model."""
    tokenized_input_sentence = source_vectorization([input_sentence])
    decoded_sentence = "[start]"

    for i in range(max_decoded_sentence_length):
        tokenized_target_sentence = target_vectorization([decoded_sentence])[:, :-1]
        predictions = transformer([tokenized_input_sentence, tokenized_target_sentence])
        
        sampled_token_index = np.argmax(predictions[0, i, :])
        sampled_token = sinahala_index_lookup.get(sampled_token_index, "[UNK]")  # Safe lookup
        
        decoded_sentence += " " + sampled_token
        if sampled_token == "[end]":
            break
    
    return decoded_sentence.replace("[start]", "").replace("[end]", "").strip()


def load_data(file_path, delimiter="\t"):
    """Loads English-Sinhala sentence pairs from a file."""
    pairs = []
    with open(file_path, "r", encoding="utf-8") as f:
        for line in f:
            parts = line.strip().split(delimiter)
            if len(parts) == 2:
                pairs.append((parts[0], parts[1]))
    return pairs

# Load test data
test_pairs = load_data("reports/test.en-ta")

# Evaluate model accuracy using BLEU score
bleu_scores = []
for eng_sentence, true_sinhala in test_pairs:
    predicted_sinhala = decode_sequence(eng_sentence)
    reference = [true_sinhala.split()]  # Reference translation
    candidate = predicted_sinhala.split()  # Model's translation
    bleu = sentence_bleu(reference, candidate)
    bleu_scores.append(bleu)
    
    print("-")
    print(f"English: {eng_sentence}")
    print(f"Predicted Sinhala: {predicted_sinhala}")
    print(f"Actual Sinhala: {true_sinhala}")
    print(f"BLEU Score: {bleu:.4f}")

# Compute and print overall average BLEU score
average_bleu = np.mean(bleu_scores)
print(f"\nOverall Average BLEU Score: {average_bleu:.4f}")

# Check vocabulary statistics
unk_count = sinahala_vocab.count("[UNK]")
print(f"Total [UNK] tokens in vocabulary: {unk_count}")
