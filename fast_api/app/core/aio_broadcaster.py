
import asyncio
from typing import List
from fastapi import WebSocket

class Broadcaster:
    def __init__(self):
        self.connections: List[WebSocket] = []

    async def connect(self, websocket: WebSocket):
        await websocket.accept()
        self.connections.append(websocket)

    def disconnect(self, websocket: WebSocket):
        self.connections.remove(websocket)

    async def broadcast(self, data: dict):
        for connection in self.connections:
            await connection.send_json(data)

# Create a single instance to be used across the app
aio_broadcaster = Broadcaster()