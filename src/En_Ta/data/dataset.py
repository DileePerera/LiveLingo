import tensorflow as tf
from .vectorize import source_vectorization, target_vectorization


batch_size = 64

def format_dataset(eng, tam):
    eng = source_vectorization(eng)
    tam = target_vectorization(tam)
    return ({"english": eng, "tamil": tam[:, :-1]}, tam[:, 1:])

def make_dataset(pairs):
    eng_texts, tam_texts = zip(*pairs)
    eng_texts = list(eng_texts)
    tam_texts = list(tam_texts)
    
    dataset = tf.data.Dataset.from_tensor_slices((eng_texts, tam_texts))
    dataset = dataset.batch(batch_size)
    dataset = dataset.map(format_dataset, num_parallel_calls=tf.data.AUTOTUNE)
    
    return dataset.shuffle(2048).prefetch(tf.data.AUTOTUNE).cache()

def load_data(file_path, delimiter="\t"):
    """Loads English-tamil sentence pairs from a file."""
    pairs = []
    with open(file_path, "r", encoding="utf-8") as f:
        for line in f:
            parts = line.strip().split(delimiter)
            if len(parts) == 2:  # Ensure it contains both English & tamil
                pairs.append((parts[0], parts[1]))
    return pairs

# Load training and validation data
tamil_train_pairs = load_data("reports/train.en-ta")
tamil_val_pairs = load_data("reports/val.en-ta")

# Create datasets
train_data = make_dataset(tamil_train_pairs)
val_data = make_dataset(tamil_val_pairs)

# Print dataset shapes for debugging
for inputs, targets in train_data.take(1):
    print(f"inputs['english'].shape: {inputs['english'].shape}")
    print(f"inputs['tamil'].shape: {inputs['tamil'].shape}")
    print(f"targets.shape: {targets.shape}")

# Save dataset to a file
def save_dataset(dataset, filename, num_samples=5):
    with open(filename, "w", encoding="utf-8") as f:
        for inputs, targets in dataset.take(num_samples):
            for eng_vec, tam_vec in zip(inputs["english"].numpy(), inputs["tamil"].numpy()):
                f.write(f"English: {eng_vec.tolist()}\ntamil: {tam_vec.tolist()}\n\n")

save_dataset(train_data, "reports/vectorized_train_data.txt", num_samples=5)
print("✅ Vectorized training data saved to 'vectorized_train_data.txt'.")
