# FAST_API/app/core/recognition_logic.py

import cv2
import torch
from PIL import Image
import io

from app import config

# Global models to be initialized by main.py
mtcnn = None
resnet = None
face_db = {}

def set_models(mtcnn_model, resnet_model, db_data):
    """Initializes global models for this module."""
    global mtcnn, resnet, face_db
    mtcnn = mtcnn_model
    resnet = resnet_model
    face_db = db_data

def recognize_faces(image: Image.Image):
    """
    Core function to recognize faces in a PIL Image.
    This combines the logic from your static and real-time recognizers.
    Returns a list of dictionaries with face info.
    """
    # Detect faces and get bounding boxes
    boxes, _ = mtcnn.detect(image)
    
    if boxes is None:
        return []

    # Extract aligned face tensors from the detected boxes
    faces = mtcnn.extract(image, boxes, None)
    if faces is None:
        return []

    # Generate embeddings for all detected faces in a single batch
    with torch.no_grad():
        embeddings = resnet(faces.to(config.DEVICE))

    recognized_faces = []
    for i, emb in enumerate(embeddings):
        min_dist = float('inf')
        best_match_name = "Unknown"
        
        # Compare the new embedding with all known embeddings
        for name, db_emb in face_db.items():
            dist = (emb - db_emb).norm().item()
            if dist < min_dist:
                min_dist = dist
                best_match_name = name
        
        # Check if the match is below the threshold
        if min_dist < config.RECOGNITION_THRESHOLD:
            recognized_faces.append({
                'name': best_match_name,
                'distance': min_dist,
                'location': boxes[i]
            })
        else:
            recognized_faces.append({
                'name': 'Unknown',
                'distance': min_dist,
                'location': boxes[i]
            })
    return recognized_faces

def draw_on_frame(frame: cv2.Mat, recognized_faces: list):
    """Draws bounding boxes and labels on a video frame."""
    for face in recognized_faces:
        x1, y1, x2, y2 = [int(coord) for coord in face['location']]
        name = face['name']
        distance = face['distance']
        
        color = (0, 255, 0) if name != 'Unknown' else (0, 0, 255)
        
        cv2.rectangle(frame, (x1, y1), (x2, y2), color, 2)
        label = f"{name} ({distance:.2f})"
        cv2.putText(frame, label, (x1, y1 - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.7, color, 2)
        
    return frame