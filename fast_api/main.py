# FAST_API/main.py

import os
import torch
from fastapi import FastAPI
from facenet_pytorch import MTCNN, InceptionResnetV1
from fastapi.middleware.cors import CORSMiddleware # Make sure this is imported

# Import modules from our 'app' package
from app import config
from app.core import recognition_logic
from app.api import admin_api, recognition_api

# Create the FastAPI app instance
app = FastAPI(
    title="Face Recognition API",
    description="A unified API for database creation, static recognition, and real-time streaming.",
    version="3.0.0"
)

# === THE FIX IS IN THIS BLOCK ===
# Add CORS Middleware to allow communication from other web pages
origins = ["*"] # Allows all origins for easy development
app.add_middleware(
    CORSMiddleware,
    allow_origins=origins,
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)
# ==============================

# Lifespan Event for Model Loading
@app.on_event("startup")
def load_models_and_db():
    # ... (Your startup logic is fine) ...
    print(f"Running on device: {config.DEVICE}")
    mtcnn_model = MTCNN(keep_all=True, device=config.DEVICE, min_face_size=20)
    resnet_model = InceptionResnetV1(pretrained='vggface2').eval().to(config.DEVICE)
    print("Models loaded successfully.")
    db_data = {}
    if os.path.exists(config.DB_SAVE_PATH):
        try:
            db_data = torch.load(config.DB_SAVE_PATH, map_location=config.DEVICE)
            print("Face database loaded successfully.")
        except Exception as e:
            print(f"Could not load face database: {e}")
    else:
        print("Face database not found. Use the '/admin/create-database' endpoint to create it.")
    recognition_logic.set_models(mtcnn_model, resnet_model, db_data)

# Include the API Routers
app.include_router(admin_api.router, prefix="/admin", tags=["Admin"])
app.include_router(recognition_api.router, tags=["Recognition & Streaming"])

@app.get("/", include_in_schema=False)
def root():
    return {"message": "Face Recognition API is running. Go to /docs for documentation."}