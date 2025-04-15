import tensorflow as tf
from tensorflow import keras
from tensorflow.keras import layers

class TransformerDecoder(layers.Layer):
    def __init__(self, embed_dim: int, dense_dim: int, num_heads: int, **kwargs):
        super().__init__(**kwargs)
        self.embed_dim = embed_dim
        self.dense_dim = dense_dim
        self.num_heads = num_heads

        # Multi-head attention for masked self-attention
        self.attention_1 = layers.MultiHeadAttention(num_heads=num_heads, key_dim=embed_dim)
        # Multi-head attention for encoder-decoder attention
        self.attention_2 = layers.MultiHeadAttention(num_heads=num_heads, key_dim=embed_dim)

        # Feed-forward network
        self.dense_proj = keras.Sequential([
            layers.Dense(dense_dim, activation="relu"),
            layers.Dense(embed_dim),
        ])

        # Layer Normalization
        self.layernorm_1 = layers.LayerNormalization()
        self.layernorm_2 = layers.LayerNormalization()
        self.layernorm_3 = layers.LayerNormalization()

        self.supports_masking = True

    def get_causal_attention_mask(self, inputs: tf.Tensor) -> tf.Tensor:
        """
        Generates a lower triangular matrix to prevent tokens from attending to future tokens.
        """
        seq_len = tf.shape(inputs)[1]
        mask = tf.linalg.band_part(tf.ones((seq_len, seq_len)), -1, 0)  # Lower triangular matrix
        return tf.expand_dims(mask, axis=0)  # Shape: (1, seq_len, seq_len)

    def call(self, inputs: tf.Tensor, encoder_outputs: tf.Tensor, mask: tf.Tensor = None) -> tf.Tensor:
        """
        Forward pass of the Transformer Decoder.
        - inputs: target sequences
        - encoder_outputs: encoded representations from the encoder
        - mask: padding mask (if any)
        """
        causal_mask = self.get_causal_attention_mask(inputs)

        if mask is not None:
            padding_mask = tf.cast(mask[:, tf.newaxis, :], dtype="int32")  # Convert mask shape
            combined_mask = tf.minimum(padding_mask, causal_mask)  # Combine masks
        else:
            combined_mask = causal_mask

        # Self-attention with causal masking
        attention_output_1 = self.attention_1(
            query=inputs, key=inputs, value=inputs, attention_mask=causal_mask
        )
        attention_output_1 = self.layernorm_1(inputs + attention_output_1)

        # Encoder-decoder attention
        attention_output_2 = self.attention_2(
            query=attention_output_1,
            key=encoder_outputs,
            value=encoder_outputs,
            attention_mask=combined_mask,
        )
        attention_output_2 = self.layernorm_2(attention_output_1 + attention_output_2)

        # Feed-forward network with residual connection
        proj_output = self.dense_proj(attention_output_2)
        return self.layernorm_3(attention_output_2 + proj_output)

    def get_config(self):
        config = super().get_config()
        config.update({
            "embed_dim": self.embed_dim,
            "num_heads": self.num_heads,
            "dense_dim": self.dense_dim,
        })
        return config

# Create a TransformerDecoder layer
decoder_layer = TransformerDecoder(embed_dim=128, dense_dim=256, num_heads=4, name="decoder_layer") 
print(decoder_layer)