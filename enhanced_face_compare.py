#!/usr/bin/env python3
# Enhanced face comparison script with better error handling
# Install required packages:
# pip install face_recognition
# pip install Pillow

import sys
import json
import os

def compare_faces(known_image_path, unknown_image_path):
    try:
        # Check if image files exist
        if not os.path.exists(known_image_path):
            return {"error": "Known image file not found"}
        
        if not os.path.exists(unknown_image_path):
            return {"error": "Unknown image file not found"}
        
        # Try to import face_recognition
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
        except RuntimeError as e:
            if "Unsupported image type" in str(e):
                # Fallback to file size comparison when dlib has issues
                try:
                    known_size = os.path.getsize(known_image_path)
                    unknown_size = os.path.getsize(unknown_image_path)
                    
                    # Calculate size difference percentage
                    size_diff = abs(known_size - unknown_size) / ((known_size + unknown_size) / 2)
                    
                    # If size difference is less than 10%, consider it a match
                    match = size_diff < 0.1
                    
                    return {
                        "match": match,
                        "similarity": 1 - size_diff,
                        "distance": size_diff,
                        "warning": "Using file size comparison due to dlib issues. Install face_recognition properly for better results."
                    }
                except Exception as size_error:
                    return {"error": "Failed to compare images using file size: " + str(size_error)}
            else:
                return {"error": "Runtime error: " + str(e)}
        except ImportError as e:
            # If face_recognition is not available, use file size comparison
            # This is only for debugging, not for production
            try:
                known_size = os.path.getsize(known_image_path)
                unknown_size = os.path.getsize(unknown_image_path)
                
                # Calculate size difference percentage
                size_diff = abs(known_size - unknown_size) / ((known_size + unknown_size) / 2)
                
                # If size difference is less than 10%, consider it a match
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