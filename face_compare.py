#!/usr/bin/env python3
# Face comparison script using face_recognition library
# Install required packages:
# pip install face_recognition
# pip install Pillow

import sys
import json
import os

def compare_faces(known_image_path, unknown_image_path):
    try:
        # Cek apakah file gambar ada
        if not os.path.exists(known_image_path):
            return {"error": "Known image file not found"}
        
        if not os.path.exists(unknown_image_path):
            return {"error": "Unknown image file not found"}
        
        # Coba import face_recognition
        try:
            import face_recognition
            from PIL import Image
            
            # Load the images
            known_image = face_recognition.load_image_file(known_image_path)
            unknown_image = face_recognition.load_image_file(unknown_image_path)
            
            # Get face encodings
            known_encodings = face_recognition.face_encodings(known_image)
            unknown_encodings = face_recognition.face_encodings(unknown_image)
            
            # Check if faces are found
            if len(known_encodings) == 0:
                return {"error": "No face found in known image"}
            
            if len(unknown_encodings) == 0:
                return {"error": "No face found in unknown image"}
            
            # Compare faces
            results = face_recognition.compare_faces([known_encodings[0]], unknown_encodings[0])
            distance = face_recognition.face_distance([known_encodings[0]], unknown_encodings[0])
            
            similarity = 1 - distance[0]  # Convert distance to similarity score
            
            return {
                "match": bool(results[0]),
                "similarity": float(similarity),
                "distance": float(distance[0])
            }
        except ImportError as e:
            # Jika face_recognition tidak tersedia, gunakan pendekatan sederhana
            # dengan membandingkan ukuran file dan nama file
            # Ini hanya untuk debugging, bukan untuk produksi
            try:
                known_size = os.path.getsize(known_image_path)
                unknown_size = os.path.getsize(unknown_image_path)
                
                # Hitung perbedaan ukuran dalam persentase
                size_diff = abs(known_size - unknown_size) / ((known_size + unknown_size) / 2)
                
                # Jika perbedaan ukuran kurang dari 10%, anggap sebagai match
                match = size_diff < 0.1
                
                return {
                    "match": match,
                    "similarity": 1 - size_diff,
                    "distance": size_diff,
                    "warning": "Using file size comparison - not accurate. Install face_recognition for better results."
                }
            except Exception as size_error:
                return {"error": "Failed to compare images using file size: " + str(size_error)}
    except Exception as e:
        return {"error": str(e)}

if __name__ == "__main__":
    if len(sys.argv) != 3:
        print(json.dumps({"error": "Usage: python face_compare.py <known_image> <unknown_image>"}))
        sys.exit(1)
    
    known_image_path = sys.argv[1]
    unknown_image_path = sys.argv[2]
    
    result = compare_faces(known_image_path, unknown_image_path)
    print(json.dumps(result))