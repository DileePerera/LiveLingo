import pandas as pd
import random

def split_pairs_method(df1, output_file):
    text_pairs = [
        (str(row.english).strip(), f"[start] {str(row.tamil).strip()} [end]")
        for row in df1.itertuples(index=False)
    ]
    
    # Shuffle text pairs
    random.shuffle(text_pairs)

    # Write to file with tab separation and no quotes
    with open(output_file, 'w', encoding='utf-8') as f_out:
        f_out.write("\n".join([f"{eng}\t{lang}" for eng, lang in text_pairs]) + "\n")

    return text_pairs

if __name__ == "__main__":
    src_file = "data/raw/tamil_english.csv"
    
    # Load dataset, handle missing values
    df1 = pd.read_csv(src_file).fillna("")

    # Process and save
    split_pairs_method(df1, "reports/output.en-ta")

    print("File saved successfully: reports/output.en-ta")