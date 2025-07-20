# In FAST_API/app/api/recognition_api.py

import cv2
from fastapi import APIRouter, File, UploadFile, HTTPException
from fastapi.responses import StreamingResponse, HTMLResponse, JSONResponse
from PIL import Image
import io

from app.core import recognition_logic

router = APIRouter()

# === THIS IS THE NEW, MISSING ENDPOINT ===
@router.post("/capture-and-recognize", tags=["Recognition"])
async def capture_and_recognize(file: UploadFile = File(...)):
    """
    Receives a single captured photo from the Laravel UI, recognizes faces,
    and returns JSON results. This is for the "Scan on Demand" feature.
    """
    if not recognition_logic.face_db:
        raise HTTPException(status_code=503, detail="Face database is not loaded.")
    
    # Read the image bytes from the uploaded file
    image_bytes = await file.read()
    
    # Use the core logic function to process the image
    img = Image.open(io.BytesIO(image_bytes)).convert("RGB")
    recognized_faces = recognition_logic.recognize_faces(img)
    
    # Convert numpy arrays in the location to lists for JSON serialization
    for face in recognized_faces:
        if 'location' in face and hasattr(face['location'], 'tolist'):
            face['location'] = face['location'].tolist()

    return JSONResponse(content={"recognized_faces": recognized_faces})
# ==========================================


# --- YOUR EXISTING ENDPOINTS (They can stay) ---

# --- Endpoint for general purpose single image upload ---
@router.post("/recognize-image", tags=["Recognition"])
async def recognize_from_upload(file: UploadFile = File(...)):
    """
    Receives an uploaded image, recognizes faces, and returns JSON results.
    """
    if not recognition_logic.face_db:
        raise HTTPException(status_code=503, detail="Face database is not loaded.")
        
    image_bytes = await file.read()
    img = Image.open(io.BytesIO(image_bytes)).convert("RGB")
    
    recognized_faces = recognition_logic.recognize_faces(img)
    
    for face in recognized_faces:
        if 'location' in face and hasattr(face['location'], 'tolist'):
            face['location'] = face['location'].tolist()
            
    return JSONResponse(content={"recognized_faces": recognized_faces})


# --- Endpoints for Real-Time Streaming ---
async def stream_generator():
    """Generator for real-time video frames from the server's camera."""
    # Try different camera backends and indices
    cap = None
    camera_backends = [
        (0, cv2.CAP_DSHOW),    # DirectShow (Windows)
        (0, cv2.CAP_MSMF),     # Media Foundation (Windows)
        (0, cv2.CAP_ANY),      # Any available backend
        (1, cv2.CAP_DSHOW),    # Try second camera with DirectShow
        (1, cv2.CAP_MSMF),     # Try second camera with Media Foundation
    ]
    
    for camera_index, backend in camera_backends:
        try:
            cap = cv2.VideoCapture(camera_index, backend)
            if cap.isOpened():
                # Test if we can actually read from the camera
                ret, test_frame = cap.read()
                if ret and test_frame is not None:
                    print(f"Successfully opened camera {camera_index} with backend {backend}")
                    break
                else:
                    cap.release()
                    cap = None
            else:
                if cap:
                    cap.release()
                cap = None
        except Exception as e:
            print(f"Failed to open camera {camera_index} with backend {backend}: {e}")
            if cap:
                cap.release()
            cap = None
    
    if cap is None:
        print("Error: Cannot open any system webcam. Please check if:")
        print("1. A webcam is connected and working")
        print("2. No other application is using the webcam")
        print("3. Webcam drivers are properly installed")
        return
    
    try:
        # Set camera properties for better performance
        cap.set(cv2.CAP_PROP_FRAME_WIDTH, 640)
        cap.set(cv2.CAP_PROP_FRAME_HEIGHT, 480)
        cap.set(cv2.CAP_PROP_FPS, 30)
        
        while True:
            success, frame = cap.read()
            if not success:
                print("Failed to read frame from camera")
                break
        
            img_rgb = Image.fromarray(cv2.cvtColor(frame, cv2.COLOR_BGR2RGB))
            recognized_faces = recognition_logic.recognize_faces(img_rgb)
            processed_frame = recognition_logic.draw_on_frame(frame, recognized_faces)
            
            ret, buffer = cv2.imencode('.jpg', processed_frame)
            if not ret:
                continue
            
            frame_bytes = buffer.tobytes()
            yield (b'--frame\r\n'
                   b'Content-Type: image/jpeg\r\n\r\n' + frame_bytes + b'\r\n')
    
    except Exception as e:
        print(f"Error in stream generator: {e}")
    finally:
        if cap:
            cap.release()
            print("Camera released")

@router.get("/video_feed", tags=["Streaming"])
def video_feed():
    """Streams processed video from the server's webcam in real-time."""
    return StreamingResponse(
        stream_generator(),
        media_type='multipart/x-mixed-replace; boundary=frame'
    )

@router.get("/realtime", response_class=HTMLResponse, tags=["Streaming"])
async def realtime_page():
    """Serves a simple HTML page to display the real-time video stream."""
    return """
    <html><head><title>Real-Time Face Recognition</title></head>
    <body><h1>Real-Time Face Recognition Stream</h1><img src="/video_feed" width="640" height="480"></body>
    </html>
    """

# --- New Camera Test Endpoint ---
@router.get("/test-camera", tags=["Testing"])
async def test_camera():
    """Test if camera is available and working."""
    camera_info = []
    
    # Test different camera indices and backends
    for camera_index in range(3):  # Test first 3 camera indices
        for backend_name, backend in [("DirectShow", cv2.CAP_DSHOW), ("MediaFoundation", cv2.CAP_MSMF), ("Any", cv2.CAP_ANY)]:
            try:
                cap = cv2.VideoCapture(camera_index, backend)
                if cap.isOpened():
                    ret, frame = cap.read()
                    if ret and frame is not None:
                        height, width = frame.shape[:2]
                        camera_info.append({
                            "index": camera_index,
                            "backend": backend_name,
                            "status": "Working",
                            "resolution": f"{width}x{height}"
                        })
                    else:
                        camera_info.append({
                            "index": camera_index,
                            "backend": backend_name,
                            "status": "Opened but cannot read"
                        })
                    cap.release()
                else:
                    camera_info.append({
                        "index": camera_index,
                        "backend": backend_name,
                        "status": "Cannot open"
                    })
            except Exception as e:
                camera_info.append({
                    "index": camera_index,
                    "backend": backend_name,
                    "status": f"Error: {str(e)}"
                })
    
    return JSONResponse(content={"camera_test_results": camera_info})