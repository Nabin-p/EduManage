# In app/api/stream.py

import asyncio
import cv2
from fastapi import APIRouter, WebSocket, WebSocketDisconnect, HTTPException
from fastapi.responses import StreamingResponse
from starlette.concurrency import run_in_threadpool

# Import the instance directly
from app.core.aio_broadcaster import aio_broadcaster
from app.core.recognition_logic import recognize_and_draw_on_frame

router = APIRouter()


# --- WebSocket Endpoint ---
@router.websocket("/ws/recognized-faces")
async def websocket_endpoint(websocket: WebSocket):
    await aio_broadcaster.connect(websocket)
    try:
        while True:
            await asyncio.sleep(1)
    except WebSocketDisconnect:
        aio_broadcaster.disconnect(websocket)


# --- This is a helper function that contains all the slow, blocking code ---
def process_frame_blocking(frame):
    """
    This function contains all the CPU-intensive, blocking operations.
    It will be run in a separate thread.
    """
    processed_frame, recognized_ids = recognize_and_draw_on_frame(frame)
    ret, buffer = cv2.imencode('.jpg', processed_frame)
    if not ret:
        return None, None
    return buffer.tobytes(), recognized_ids


# --- Real-Time Streaming Endpoint (MODIFIED to be non-blocking) ---
async def generate_frames():
    cap = cv2.VideoCapture(0)
    if not cap.isOpened():
        print("Error: Cannot open webcam")
        return

    while True:
        success, frame = cap.read()
        if not success:
            break
        
        # Run the slow function in a separate thread pool to not block the server
        frame_bytes, recognized_ids = await run_in_threadpool(process_frame_blocking, frame)
        
        if frame_bytes is None:
            continue

        if recognized_ids:
            asyncio.create_task(aio_broadcaster.broadcast({"recognized_ids": recognized_ids}))
        
        yield (b'--frame\r\n'
               b'Content-Type: image/jpeg\r\n\r\n' + frame_bytes + b'\r\n')
    
    cap.release()


@router.get("/video-feed", tags=["Streaming"])
def video_feed():
    if recognize_and_draw_on_frame is None:
        raise HTTPException(status_code=503, detail="Recognition logic not loaded.")
    return StreamingResponse(generate_frames(), media_type='multipart/x-mixed-replace; boundary=frame')