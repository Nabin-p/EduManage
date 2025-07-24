import cv2
import torch
from PIL import Image
import io

from app import config

# Global models to be initialized at startup
mtcnn = None
resnet = None
face_db = {}

def set_models(mtcnn_model, resnet_model, db_data):
    """Initializes global models for this module."""
    global mtcnn, resnet, face_db
    mtcnn = mtcnn_model
    resnet = resnet_model
    face_db = db_data

def recognize_faces_in_image(image_bytes: bytes):
    """Recognizes faces in a single image provided as bytes."""
    # This function is for single image upload and remains unchanged.
    img = Image.open(io.BytesIO(image_bytes)).convert('RGB')
    
    boxes, _ = mtcnn.detect(img)
    if boxes is None:
        return []

    faces = mtcnn.extract(img, boxes, None)
    if faces is None:
        return []

    with torch.no_grad():
        embeddings = resnet(faces.to(config.DEVICE)).detach().cpu()

    recognized_faces = []
    for i, emb in enumerate(embeddings):
        min_dist = float('inf')
        best_match_name = "Unknown"
        
        for name, db_emb in face_db.items():
            dist = (emb - db_emb.cpu()).norm().item()
            if dist < min_dist:
                min_dist = dist
                best_match_name = name
        
        if min_dist < config.RECOGNITION_THRESHOLD:
            recognized_faces.append(best_match_name)
        else:
            recognized_faces.append('Unknown')
            
    return recognized_faces

# ====================================================================
#          MODIFIED FUNCTION FOR REAL-TIME STREAMING
# ====================================================================
def recognize_and_draw_on_frame(frame):
    """Detects, recognizes, draws boxes, AND returns a list of recognized IDs."""
    if mtcnn is None or resnet is None:
        return frame, [] # Return early if models aren't ready

    img_rgb = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
    
    # --- START OF MODIFICATION ---
    recognized_ids_in_frame = [] # Create a list to store recognized IDs for this frame
    # --- END OF MODIFICATION ---
    
    boxes, _ = mtcnn.detect(img_rgb)
    
    if boxes is not None:
        faces = mtcnn.extract(img_rgb, boxes, None)
        if faces is not None:
            with torch.no_grad():
                embeddings = resnet(faces.to(config.DEVICE)).detach()

            for i, emb in enumerate(embeddings):
                min_dist = float('inf')
                best_match_name = "Unknown"
                
                # Compare with the face database
                for db_id, db_emb in face_db.items():
                    dist = (emb - db_emb).norm().item()
                    if dist < min_dist:
                        min_dist = dist
                        best_match_name = db_id
                
                x1, y1, x2, y2 = [int(c) for c in boxes[i]]
                
                if min_dist < config.RECOGNITION_THRESHOLD:
                    name_to_display = best_match_name
                    color = (0, 255, 0) # Green for recognized
                    
                    # --- START OF MODIFICATION ---
                    # Add the recognized ID to our list for this frame
                    recognized_ids_in_frame.append(best_match_name)
                    # --- END OF MODIFICATION ---
                else:
                    name_to_display = "Unknown"
                    color = (0, 0, 255) # Red for unknown
                
                # Draw rectangle and label on the frame
                cv2.rectangle(frame, (x1, y1), (x2, y2), color, 2)
                label = f"{name_to_display} ({min_dist:.2f})"
                cv2.putText(frame, label, (x1, y1 - 10), cv2.FONT_HERSHEY_SIMPLEX, 0.7, color, 2)

    # --- START OF MODIFICATION ---
    # Return both the visual frame and the list of recognized IDs
    return frame, recognized_ids_in_frame
    # --- END OF MODIFICATION ---