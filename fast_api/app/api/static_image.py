# app/api/static_image.py
from fastapi import APIRouter, File, UploadFile, HTTPException
from fastapi.responses import JSONResponse

# Updated import path
from app.core import recognition_logic

router = APIRouter()

@router.post("/upload-image", tags=["Recognition"])
async def recognize_from_upload(file: UploadFile = File(...)):
    if not recognition_logic.face_db:
        raise HTTPException(status_code=503, detail="Face database is not loaded.")
        
    image_bytes = await file.read()
    recognized_faces = recognition_logic.recognize_faces_in_image(image_bytes)
    
    return JSONResponse(content={"recognized_faces": recognized_faces})