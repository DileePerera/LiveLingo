class Splitting:
    def splitting_method(self, text_pairs):
        """
        Splits the dataset into training, validation, and test sets.
        - 70% training, 15% validation, 15% test.
        """
        num_val_sample = int(0.15 * len(text_pairs))
        num_train_samples = len(text_pairs) - 2 * num_val_sample

        train_pairs = text_pairs[:num_train_samples]
        val_pairs = text_pairs[num_train_samples:num_train_samples + num_val_sample]
        test_pairs = text_pairs[num_train_samples + num_val_sample:]

        print("✅ Total sentence pairs:", len(text_pairs))
        print("🟢 Training set size:", len(train_pairs))
        print("🟡 Validation set size:", len(val_pairs))
        print("🔴 Test set size:", len(test_pairs))

        return train_pairs, val_pairs, test_pairs


def load_text_pairs(input_file):
    """
    Loads text pairs from a tab-separated file.
    Format: English \t Target Language
    """
    text_pairs = []
    with open(input_file, 'r', encoding='utf-8') as f:
        for line in f:
            parts = line.strip().split("\t")
            if len(parts) == 2:  # Ensure valid format
                text_pairs.append((parts[0], parts[1]))
    
    print(f"Loaded {len(text_pairs)} pairs from {input_file}")
    return text_pairs


def save_split_data(pairs, output_file):
    """
    Saves sentence pairs to a file in a tab-separated format.
    """
    with open(output_file, 'w', encoding='utf-8') as f:
        for src, tgt in pairs:
            f.write(f"{src}\t{tgt}\n")
    print(f"Saved {len(pairs)} pairs to {output_file}")


if __name__ == "__main__":
    # Load the sentence pairs from the OPUS dataset file
    text_pairs = load_text_pairs("reports/output.en-ta")

    # Split the dataset
    splitter = Splitting()
    train_pairs, val_pairs, test_pairs = splitter.splitting_method(text_pairs)

    # Save the split datasets
    save_split_data(train_pairs, "reports/train.en-ta")
    save_split_data(val_pairs, "reports/val.en-ta")
    save_split_data(test_pairs, "reports/test.en-ta")
