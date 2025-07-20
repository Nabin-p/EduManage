import os
import io
import torch
import cv2
import numpy as np
from PIL import Image
from facenet_pytorch import MTCNN, InceptionResnetV1
from fastapi import FastAPI, File, UploadFile, HTTPException
from fastapi.responses import JSONResponse, StreamingResponse

# ---- 1. CONFIGURATION ----
DATABASE_DIR = "known_faces"
DB_SAVE_PATH = 'face_db.pt'
DEVICE = torch.device('cuda:0' if torch.cuda.is_available() else 'cpu')

# ---- 2. GLOBAL MODELS & DATABASE ----
# These will be loaded once at startup
mtcnn: MTCNN = None
resnet: InceptionResnetV1 = None
face_db: dict = {}

# ---- 3. FASTAPI APP INITIALIZATION ----
app = FastAPI(
    title="Face Recognition API",
    description="An API for real-time face recognition using FaceNet.",
    version="1.0.0"
)

# ---- 4. LIFESPAN EVENTS (STARTUP & SHUTDOWN) ----
@app.on_event("startup")
def startup_event():
    """
    Load models and face database at application startup.
    """
    global mtcnn, resnet, face_db, DEVICE
    print(f"Running on device: {DEVICE}")

    # Load face detection and recognition models
    mtcnn = MTCNN(keep_all=True, device=DEVICE)
    resnet = InceptionResnetV1(pretrained='vggface2').eval().to(DEVICE)
    print("Models loaded successfully.")

    # Load the face database if it exists
    if os.path.exists(DB_SAVE_PATH):
        try:
            face_db = torch.load(DB_SAVE_PATH, map_location=DEVICE)
            print("Face database loaded successfully.")
            print("Known individuals:", list(face_db.keys()))
        except Exception as e:
            print(f"Could not load face database: {e}")
    else:
        print("Face database not found. Please create it using the /admin/create-database endpoint.")


# ---- 5. HELPER FUNCTIONS (CORE LOGIC) ----
def create_face_database_logic():
    """
    The core logic to create the face database.
    This is called by the API endpoint.
    """
    global face_db # We need to modify the global variable
    
    if not os.path.isdir(DATABASE_DIR):
        raise HTTPException(status_code=404, detail=f"Database directory '{DATABASE_DIR}' not found.")

    new_face_db = {}
    for person_name in os.listdir(DATABASE_DIR):
        person_dir = os.path.join(DATABASE_DIR, person_name)
        if os.path.isdir(person_dir):
            embeddings = []
            for img_file in os.listdir(person_dir):
                img_path = os.path.join(person_dir, img_file)
                try:
                    img = Image.open(img_path).convert('RGB')
                    faces = mtcnn(img)
                    if faces is not None:
                        with torch.no_grad():
                            face_embeddings = resnet(faces.to(DEVICE)).detach().cpu()
                        embeddings.append(face_embeddings)
                except Exception as e:
                    print(f"Error processing {img_path}: {e}")
            
            if embeddings:
                all_embeddings = torch.cat(embeddings)
                new_face_db[person_name] = all_embeddings.mean(dim=0)
                print(f"Processed '{person_name}' with {len(all_embeddings)} face samples.")

    if new_face_db:
        torch.save(new_face_db, DB_SAVE_PATH)
        face_db = new_face_db # Update the in-memory database
        return {"status": "success", "message": f"Database created with {len(new_face_db)} people."}
    else:
        raise HTTPException(status_code=500, detail="Could not create database. No valid faces found.")

def recognize_faces_logic(image_bytes: bytes, threshold=0.7):
    """
    The core logic to recognize faces in an uploaded image.
    """
    img = Image.open(io.BytesIO(image_bytes)).convert('RGB')
    
    # Detect faces
    boxes, _ = mtcnn.detect(img)
    
    if boxes is None:
        return []

    # Get aligned face tensors
    faces = mtcnn.extract(img, boxes, None)
    if faces is None:
        return []

    # Generate embeddings
    with torch.no_grad():
        embeddings = resnet(faces.to(DEVICE)).detach().cpu()

    recognized_faces = []
    for i, emb in enumerate(embeddings):
        min_dist = float('inf')
        best_match_name = "Unknown"
        
        for name, db_emb in face_db.items():
            dist = (emb - db_emb.cpu()).norm().item()
            if dist < min_dist:
                min_dist = dist
                best_match_name = name
        
        box = [int(coord) for coord in boxes[i]]
        if min_dist < threshold:
            recognized_faces.append({
                'name': best_match_name,
                'distance': round(min_dist, 4),
                'box': box
            })
        else:
            recognized_faces.append({
                'name': 'Unknown',
                'distance': round(min_dist, 4),
                'box': box
            })
    return recognized_faces

# ---- 6. API ENDPOINTS ----
@app.post("/admin/create-database", tags=["Admin"])
def create_database_endpoint():
    """
    Scans the `known_faces` directory, creates face embeddings,
    and saves them to `face_db.pt`. This will overwrite any existing database.
    """
    return create_face_database_logic()

@app.post("/recognize", tags=["Recognition"])
async def recognize_image(file: UploadFile = File(...)):
    """
    Upload an image and receive a list of recognized faces,
    their bounding boxes, and the distance metric.
    """
    if not face_db:
        raise HTTPException(status_code=503, detail="Face database is not loaded. Please create it first.")

    # Read image bytes
    image_bytes = await file.read()
    
    # Perform recognition
    recognized_faces = recognize_faces_logic(image_bytes, threshold=0.7)
    
    return JSONResponse(content={"recognized_faces": recognized_faces})

@app.get("/", include_in_schema=False)
def root():
    return {"message": "Face Recognition API is running. Go to /docs for documentation."}