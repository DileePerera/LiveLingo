import tensorflow as tf
from tensorflow import keras
from tensorflow.keras import layers

class TransformerEncoder(layers.Layer):
    def __init__(self, embed_dim: int, dense_dim: int, num_heads: int, **kwargs):
        super().__init__(**kwargs)
        self.embed_dim = embed_dim
        self.dense_dim = dense_dim
        self.num_heads = num_heads

        # Multi-Head Self Attention
        self.attention = layers.MultiHeadAttention(num_heads=num_heads, key_dim=embed_dim)

        # Feed-forward network
        self.dense_proj = keras.Sequential([
            layers.Dense(dense_dim, activation="relu"),
            layers.Dense(embed_dim),
        ])

        # Normalization layers
        self.layernorm_1 = layers.LayerNormalization()
        self.layernorm_2 = layers.LayerNormalization()

    def call(self, inputs: tf.Tensor, mask: tf.Tensor = None) -> tf.Tensor:
        if mask is not None:
            mask = mask[:, tf.newaxis, :]  # Adjust mask for attention shape
        
        # Multi-head attention
        attention_output = self.attention(inputs, inputs, attention_mask=mask)

        # Residual connection + Layer Normalization
        proj_input = self.layernorm_1(inputs + attention_output)

        # Feed-forward network + Residual Connection
        proj_output = self.dense_proj(proj_input)

        return self.layernorm_2(proj_input + proj_output)

    def get_config(self):
        config = super().get_config()
        config.update({
            "embed_dim": self.embed_dim,
            "num_heads": self.num_heads,
            "dense_dim": self.dense_dim,
        })
        return config

# Create a TransformerEncoder layer
encoder_layer = TransformerEncoder(embed_dim=128, dense_dim=256, num_heads=4, name="encoder_layer")
print(encoder_layer)