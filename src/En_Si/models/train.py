import tensorflow as tf
from tensorflow import keras
from tensorflow.keras import layers
from end_end import transformer
import sys
import os
import datetime
import numpy as np
import nltk
from nltk.translate.bleu_score import sentence_bleu
from tensorflow.keras.optimizers import Adam
from tensorflow.keras.callbacks import CSVLogger, ModelCheckpoint, Callback

nltk.download('punkt')

sys.path.append(os.path.abspath(os.path.join(os.path.dirname(__file__), '..')))
from data.dataset import train_data, val_data 

# Create directories for logs and models
log_dir = "logs"
model_dir = "models"
os.makedirs(log_dir, exist_ok=True)
os.makedirs(model_dir, exist_ok=True)

# Define log file with timestamp
log_file = os.path.join(log_dir, f"train_log_{datetime.datetime.now().strftime('%Y%m%d_%H%M%S')}.csv")

# CSV Logger to record training progress
csv_logger = CSVLogger(log_file, append=True)

# Model Checkpoint to save the best model
model_checkpoint = ModelCheckpoint(
    os.path.join(model_dir, "best_transformer_model.h5"),
    monitor="val_loss",
    save_best_only=True,
    verbose=1
)

# Custom BLEU Score Callback
class BLEUScoreCallback(Callback):
    def __init__(self, val_data, log_file):
        super().__init__()
        self.val_data = val_data
        self.log_file = log_file

    def on_epoch_end(self, epoch, logs=None):
        references, hypotheses = [], []
        for batch in self.val_data.take(10):  # Evaluate on a small validation sample
            input_texts, target_texts = batch  # Assuming (source, target) pairs
            predictions = self.model.predict(input_texts, verbose=0)
            predicted_ids = np.argmax(predictions, axis=-1)  # Get the highest probability word
            
            # Convert predicted token IDs to text (Assuming a tokenizer exists)
            for i in range(len(target_texts)):
                reference = target_texts[i].numpy().tolist()  # Ground truth tokens
                hypothesis = predicted_ids[i].tolist()  # Model output tokens
                references.append([reference])  # BLEU expects list of references
                hypotheses.append(hypothesis)
        
        # Compute BLEU Score
        bleu_score = np.mean([sentence_bleu(ref, hyp) for ref, hyp in zip(references, hypotheses)])
        print(f"\nEpoch {epoch+1}: BLEU Score = {bleu_score:.4f}")
        
        # Log BLEU Score to CSV
        with open(self.log_file, "a") as f:
            f.write(f"{epoch+1},{bleu_score:.4f}\n")

# Compile the transformer model
transformer.compile(
    optimizer=Adam(learning_rate=1e-4),  
    loss="sparse_categorical_crossentropy",
    metrics=["accuracy"]
)

# Train the model with BLEU Score Logging
transformer.fit(
    train_data, 
    epochs=50, 
    validation_data=val_data, 
    callbacks=[csv_logger, model_checkpoint, BLEUScoreCallback(val_data, log_file)]
)

# Save the final model
model_path = os.path.join(model_dir, "final_transformer_model.h5")
transformer.save(model_path)

print(f"Model saved successfully to {model_path}")
print(f"Training log saved at {log_file}")
