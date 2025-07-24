# In fast_api/app/core/aio_broadcaster.py

import asyncio
from typing import List
from fastapi import WebSocket
from starlette.websockets import WebSocketDisconnect

class Broadcaster:
    def __init__(self):
        self.connections: List[WebSocket] = []
        self.lock = asyncio.Lock()

    async def connect(self, websocket: WebSocket):
        await websocket.accept()
        async with self.lock:
            self.connections.append(websocket)

    def disconnect(self, websocket: WebSocket):
        # This is a synchronous method, so we don't need a lock
        # as it won't be interrupted.
        if websocket in self.connections:
            self.connections.remove(websocket)

    async def broadcast(self, data: dict):
        # We need to make a copy of the list because we might modify it
        # inside the loop if a connection is dead.
        disconnected_clients = []
        
        async with self.lock:
            # First, iterate and send messages
            for connection in self.connections:
                try:
                    # The risky operation that might fail
                    await connection.send_json(data)
                except (WebSocketDisconnect, RuntimeError) as e:
                    # This client is dead. Add it to a list to be removed later.
                    # We don't remove it here to avoid changing the list while iterating.
                    print(f"DEBUG: Client disconnected, scheduling for removal. Error: {e}")
                    disconnected_clients.append(connection)

            # Now, remove all the dead connections we found
            if disconnected_clients:
                for client in disconnected_clients:
                    if client in self.connections:
                        self.connections.remove(client)

# Create a single, shared instance of the broadcaster
aio_broadcaster = Broadcaster()