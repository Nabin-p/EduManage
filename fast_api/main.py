# In fast_api/main.py

import os
import torch
from fastapi import FastAPI
from facenet_pytorch import MTCNN, InceptionResnetV1
from fastapi.middleware.cors import CORSMiddleware

# Import from the 'app' package
from app import config
from app.core import recognition_logic
from app.api import admin_api, stream

# Create the FastAPI app instance
app = FastAPI(
    title="Face Recognition API",
    version="2.0.0"
)

# --- CORS Middleware ---
# This allows your Laravel app (running on a different port) to connect.
origins = [
    "http://127.0.0.1:8000",
    "http://localhost:8000",
]

app.add_middleware(
    CORSMiddleware,
    allow_origins=origins,
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)
# ------------------------

# --- Lifespan Event for Model Loading ---
@app.on_event("startup")
def load_models_and_db():
    """Load models and face database at application startup."""
    print(f"Running on device: {config.DEVICE}")

    # Load models
    mtcnn_model = MTCNN(keep_all=True, device=config.DEVICE)
    resnet_model = InceptionResnetV1(pretrained='vggface2').eval().to(config.DEVICE)
    print("Models loaded successfully.")
    
    # Load face database
    db_data = {}
    if os.path.exists(config.DB_SAVE_PATH):
        try:
            db_data = torch.load(config.DB_SAVE_PATH, map_location=config.DEVICE)
            print("Face database loaded successfully.")
            print("Known individuals:", list(db_data.keys()))
        except Exception as e:
            print(f"Could not load face database: {e}")
    else:
        print("Face database not found. Please create it via the admin endpoint.")
    
    # Pass models and DB to the recognition_logic module
    recognition_logic.set_models(mtcnn_model, resnet_model, db_data)
# ------------------------------------

# --- Include API Routers ---
# This code is now correctly placed at the top level of the script.
app.include_router(admin_api.router, prefix="/admin")
app.include_router(stream.router)
# ---------------------------

# --- Health Check Endpoint (with correct indentation) ---
@app.get("/health", tags=["Health"])
def health_check():
    """Simple health check endpoint."""
    # This line is now correctly indented inside the function.
    return {"status": "ok"}
# --------------------------------------------------------