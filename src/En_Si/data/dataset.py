import tensorflow as tf
from data.vectorize import source_vectorization, target_vectorization


batch_size = 64

def format_dataset(eng, sin):
    eng = source_vectorization(eng)
    sin = target_vectorization(sin)
    return ({"english": eng, "sinhala": sin[:, :-1]}, sin[:, 1:])

def make_dataset(pairs):
    eng_texts, sin_texts = zip(*pairs)
    eng_texts = list(eng_texts)
    sin_texts = list(sin_texts)
    
    dataset = tf.data.Dataset.from_tensor_slices((eng_texts, sin_texts))
    dataset = dataset.batch(batch_size)
    dataset = dataset.map(format_dataset, num_parallel_calls=tf.data.AUTOTUNE)
    
    return dataset.shuffle(2048).prefetch(tf.data.AUTOTUNE).cache()

def load_data(file_path, delimiter="\t"):
    """Loads English-Sinhala sentence pairs from a file."""
    pairs = []
    with open(file_path, "r", encoding="utf-8") as f:
        for line in f:
            parts = line.strip().split(delimiter)
            if len(parts) == 2:  # Ensure it contains both English & Sinhala
                pairs.append((parts[0], parts[1]))
    return pairs

# Load training and validation data
sinhala_train_pairs = load_data("reports/train.en-si")
sinhala_val_pairs = load_data("reports/val.en-si")

# Create datasets
train_data = make_dataset(sinhala_train_pairs)
val_data = make_dataset(sinhala_val_pairs)

# Print dataset shapes for debugging
for inputs, targets in train_data.take(1):
    print(f"inputs['english'].shape: {inputs['english'].shape}")
    print(f"inputs['sinhala'].shape: {inputs['sinhala'].shape}")
    print(f"targets.shape: {targets.shape}")

# Save dataset to a file
def save_dataset(dataset, filename, num_samples=5):
    with open(filename, "w", encoding="utf-8") as f:
        for inputs, targets in dataset.take(num_samples):
            for eng_vec, sin_vec in zip(inputs["english"].numpy(), inputs["sinhala"].numpy()):
                f.write(f"English: {eng_vec.tolist()}\nSinhala: {sin_vec.tolist()}\n\n")

save_dataset(train_data, "reports/vectorized_train_data.txt", num_samples=5)
print("✅ Vectorized training data saved to 'vectorized_train_data.txt'.")
