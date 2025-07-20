# FAST_API/app/config.py
import torch

# Directory where student/person photos are stored
DATABASE_DIR = "known_faces"

# Path to save the processed face database file
DB_SAVE_PATH = 'face_db.pt'

# Device to run the models on
DEVICE = torch.device('cuda:0' if torch.cuda.is_available() else 'cpu')

# Confidence threshold for recognition. Lower is stricter.
RECOGNITION_THRESHOLD = 0.8