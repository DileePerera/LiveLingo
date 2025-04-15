import tensorflow as tf
from tensorflow.keras.layers import TextVectorization
import numpy as np
import json
import re

# Define characters to remove
strip_chars = "!\"#$%&'()*+,-./:;<=>?@[\\]^_`{|}~"

# Custom function for text cleaning
def custom_standardization(input_string):
    lowercase = tf.strings.lower(input_string)
    return tf.strings.regex_replace(lowercase, f"[{re.escape(strip_chars)}]", "")

# Set vocabulary size and sequence length
vocab_size = 15000
sequence_length = 20

# Initialize TextVectorization layers
source_vectorization = TextVectorization(
    max_tokens=vocab_size,
    output_mode="int",
    output_sequence_length=sequence_length,
)

target_vectorization = TextVectorization(
    max_tokens=vocab_size,
    output_mode="int",
    output_sequence_length=sequence_length + 1,
    standardize=custom_standardization,
)

# Function to load pairs from a file
def load_text_pairs(file_path):
    pairs = []
    with open(file_path, "r", encoding="utf-8") as f:
        for line in f:
            parts = line.strip().split("\t")  # Assuming tab-separated values
            if len(parts) == 2:
                pairs.append((parts[0], parts[1]))  # (English, Sinhala)
    return pairs

# Load training pairs
train_file = "reports/train.en-ta"  # Update with actual file path
tamil_train_pairs = load_text_pairs(train_file)

# Extract English and Sinhala texts
train_english_texts = [pair[0] for pair in tamil_train_pairs]
train_tamil_texts = [pair[1] for pair in tamil_train_pairs]

# Adapt vectorization layers to dataset
source_vectorization.adapt(train_english_texts)
target_vectorization.adapt(train_tamil_texts)

# Vectorize the text pairs
vectorized_english = source_vectorization(np.array(train_english_texts))
vectorized_tamil = target_vectorization(np.array(train_tamil_texts))

# Save vectorized data to a file
def save_vectorized_data(file_path, english_vectors, tamil_vectors):
    with open(file_path, "w", encoding="utf-8") as f:
        for eng, tam in zip(english_vectors.numpy(), tamil_vectors.numpy()):
            f.write(json.dumps({"english": eng.tolist(), "tamil": tam.tolist()}) + "\n")

# Save the vectorized output
vectorized_output_file = "reports/vectorized_output_ta.json"
save_vectorized_data(vectorized_output_file, vectorized_english, vectorized_tamil)

print(f"✅ Vectorized data saved to {vectorized_output_file}")
