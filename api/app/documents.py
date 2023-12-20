import os
def list(docs_path):
    documents = []
    for f in os.listdir(docs_path):
      documents.append(f)
    return documents