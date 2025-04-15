import tensorflow as tf
from tensorflow import keras
from tensorflow.keras import layers
from positional import PositionalEmbedding
from encoder import TransformerEncoder
from decoder import TransformerDecoder

# Hyperparameters
embed_dim = 256
dense_dim = 2048
num_heads = 8
vocab_size = 15000
sequence_length = 20

# Encoder
encoder_inputs = keras.Input(shape=(None,), dtype="int64", name="english")
x = PositionalEmbedding(sequence_length, vocab_size, embed_dim)(encoder_inputs)
encoder_outputs = TransformerEncoder(embed_dim, dense_dim, num_heads)(x, mask=None)

# Decoder
decoder_inputs = keras.Input(shape=(None,), dtype="int64", name="sinhala")
decoder_embedding_layer = PositionalEmbedding(sequence_length, vocab_size, embed_dim)
decoder_embeddings = decoder_embedding_layer(decoder_inputs)

# Get mask from embeddings
# encoder_mask = encoder_inputs._keras_mask  # Mask from encoder inputs
# decoder_mask = decoder_inputs._keras_mask  # Mask from decoder inputs

# Pass masks to the decoder
x = TransformerDecoder(embed_dim, dense_dim, num_heads)(decoder_embeddings, encoder_outputs, mask=None)
x = layers.Dropout(0.5)(x)

# Output layer
decoder_outputs = layers.Dense(vocab_size, activation="softmax")(x)

# Define the full Transformer model
transformer = keras.Model([encoder_inputs, decoder_inputs], decoder_outputs)


# Summary
transformer.summary()

