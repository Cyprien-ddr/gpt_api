#!/usr/bin/env python3
# https://www.makeuseof.com/openai-api-langchain-analyze-local-documents/
from pprint import pprint
from langchain.document_loaders import PyPDFLoader, TextLoader
from langchain.text_splitter import CharacterTextSplitter
from langchain.embeddings.openai import OpenAIEmbeddings
from langchain.vectorstores import FAISS
from langchain.chains import RetrievalQA
from langchain.llms import OpenAI
import os
import sys
sys.path.append(os.path.abspath(os.path.dirname(__file__) + "/../"))
from ipa_libs.config import configs

os.environ["OPENAI_API_KEY"] = configs["openai"]["api_key"]
os.environ["LANGCHAIN_TRACING_V2"] = configs["langsmith"]['tracing_v2']
os.environ["LANGCHAIN_ENDPOINT"] = configs["langsmith"]['endpoint']
os.environ["LANGCHAIN_API_KEY"] = configs["langsmith"]['api_key']
os.environ["LANGCHAIN_PROJECT"] = configs["langsmith"]['project']


def load_documents(docs_path):
    global comments 
    comments = []
    documents = []
    for f in os.listdir(docs_path):
        if f.endswith(".pdf"):
            loader = PyPDFLoader(docs_path + "/" + f)
            documents.extend(loader.load())
            print('Loaded: ' + f)
        elif f.endswith(".txt") or f.endswith(".md"):
            loader = TextLoader(docs_path + "/" + f)
            documents.extend(loader.load())
            print('Loaded: ' + f)
        else:
            if not os.path.isdir(docs_path + "/" + f):
                raise ValueError("Invalid file type")

    text_splitter = CharacterTextSplitter(chunk_size=1000,
                                          chunk_overlap=30, separator="\n")
    chunked_documents = text_splitter.split_documents(documents=documents)
    # pprint(documents[0].page_content)
    if len(chunked_documents) == 0:
        raise Exception(
            "Aucun contenu n'a pu être extrait de tous les documents fournis.")
    else:
        for doc in documents:
            if doc.page_content == '':
              if not 'page' in doc.metadata:
                  page = '0'
              else:
                  page = str(doc.metadata['page'])
              comments.append('Pas de contenu extrait de la page ' + page + ' du document ' + doc.metadata['source'])
            # else:
            #     print('UU' + doc.page_content)
    return chunked_documents


def query_pdf(query, retriever):
    qa = RetrievalQA.from_chain_type(llm=OpenAI(),
                                     chain_type="stuff", retriever=retriever)
    result = qa.run(query)
    return result


def vectorize(docs_path):
    docs = load_documents(docs_path)
    embeddings = OpenAIEmbeddings()
    vectorstore = FAISS.from_documents(docs, embeddings)
    vectorstore.save_local("faiss_index_constitution")
    persisted_vectorstore = FAISS.load_local(
        "faiss_index_constitution", embeddings)

    return persisted_vectorstore


def query(query, docs_path):
    try:
        persisted_vectorstore = vectorize(docs_path)
        result = query_pdf(query, persisted_vectorstore.as_retriever())
        return result, '\n'.join(comments)
    except Exception as error:
        return f'Erreur du LLM : {error}', '\n'.join(comments)


if __name__ == "__main__":
    if len(sys.argv) > 1:
        docs_path = sys.argv[1]
    else:
        docs_path = input(
            "Enter the path to the documents dir (containing .pdf or .txt):\n")
    try:
        persisted_vectorstore = vectorize(docs_path)
        query = input("Type in your query (type 'exit' to quit):\n")
        while query != "exit":
            result = query_pdf(query, persisted_vectorstore.as_retriever())
            if len(comments) > 0:
                print('\n'.join(comments) + '\n')
            print(result)
            query = input("Type in your query (type 'exit' to quit):\n")
    except Exception as error:
        print(f'Erreur du LLM : {error}')
