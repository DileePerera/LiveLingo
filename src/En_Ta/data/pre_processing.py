# from datasets import load_dataset

# # Load the dataset
# ds = load_dataset("jarvisvasu/english-to-colloquial-tamil")

# # Convert each split to CSV
# for split in ds.keys():
#     df = ds[split].to_pandas()  # Convert to Pandas DataFrame
#     df.to_csv(f"data/raw/english_to_colloquial_tamil.csv", index=False)

# print("Dataset saved successfully in the 'data/raw' directory.")

import pandas as pd
from csv import QUOTE_NONE

# Load the dataset
df = pd.read_csv("data/raw/english_to_colloquial_tamil.csv")

# Remove the "instruction" column
df = df.drop(columns=["instruction"], errors="ignore")

# Save the modified dataset
df.to_csv(f"data/raw/tamil_english.csv", index=False)

print("Updated dataset saved without the instruction column.")
