# app/api/admin.py
import os
import torch
from fastapi import APIRouter, HTTPException
from PIL import Image

# Updated import paths
from app import config
from app.core import recognition_logic

router = APIRouter()

@router.post("/create-database", tags=["Admin"])
def create_database_endpoint():
    # ... function content is identical, but uses the new import aliases
    if not os.path.isdir(config.DATABASE_DIR):
        raise HTTPException(status_code=404, detail=f"Database directory '{config.DATABASE_DIR}' not found.")

    new_face_db = {}
    for person_name in os.listdir(config.DATABASE_DIR):
        person_dir = os.path.join(config.DATABASE_DIR, person_name)
        if os.path.isdir(person_dir):
            embeddings = []
            for img_file in os.listdir(person_dir):
                img_path = os.path.join(person_dir, img_file)
                try:
                    img = Image.open(img_path).convert('RGB')
                    faces = recognition_logic.mtcnn(img)
                    if faces is not None:
                        with torch.no_grad():
                            face_embeddings = recognition_logic.resnet(faces.to(config.DEVICE)).detach().cpu()
                        embeddings.append(face_embeddings)
                except Exception as e:
                    print(f"Error processing {img_path}: {e}")
            
            if embeddings:
                all_embeddings = torch.cat(embeddings)
                new_face_db[person_name] = all_embeddings.mean(dim=0)

    if new_face_db:
        torch.save(new_face_db, config.DB_SAVE_PATH)
        recognition_logic.face_db.clear()
        recognition_logic.face_db.update(new_face_db)
        return {"status": "success", "message": f"Database created with {len(new_face_db)} people."}
    else:
        raise HTTPException(status_code=500, detail="Could not create database. No valid faces found.")