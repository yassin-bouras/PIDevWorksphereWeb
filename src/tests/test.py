import sys
import os
import cv2
import tempfile
import shutil

def detect_faces(image, cascade_path):
    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
    face_cascade = cv2.CascadeClassifier(cascade_path)
    if face_cascade.empty():
        print("ERROR: Could not load cascade classifier", file=sys.stderr)
        sys.exit(1)
    faces = face_cascade.detectMultiScale(gray, scaleFactor=1.1, minNeighbors=5)
    return faces

def compare_faces(stored_img, live_img, cascade_path):
    stored_faces = detect_faces(stored_img, cascade_path)
    live_faces = detect_faces(live_img, cascade_path)

    if len(stored_faces) == 0 or len(live_faces) == 0:
        return False

    (x, y, w, h) = stored_faces[0]
    stored_roi = stored_img[y:y+h, x:x+w]
    (x, y, w, h) = live_faces[0]
    live_roi = live_img[y:y+h, x:x+w]

    stored_roi = cv2.resize(stored_roi, (100, 100))
    live_roi = cv2.resize(live_roi, (100, 100))

    stored_gray = cv2.cvtColor(stored_roi, cv2.COLOR_BGR2GRAY)
    live_gray = cv2.cvtColor(live_roi, cv2.COLOR_BGR2GRAY)

    diff = cv2.norm(stored_gray, live_gray)
    threshold = 3000  # You can adjust this value if needed

    return diff < threshold

def compare_faces_with_directory(faces_dir, live_image_path, cascade_path):
    if not os.path.isdir(faces_dir):
        print("ERROR: Faces directory not found", file=sys.stderr)
        return None

    if not os.path.isfile(live_image_path):
        print("ERROR: Live image not found", file=sys.stderr)
        return None

    live_img = cv2.imread(live_image_path)
    if live_img is None:
        print("ERROR: Failed to load live image", file=sys.stderr)
        return None

    for filename in os.listdir(faces_dir):
        if filename.lower().endswith(('.png', '.jpg', '.jpeg')):
            stored_path = os.path.join(faces_dir, filename)
            stored_img = cv2.imread(stored_path)

            if stored_img is None:
                print(f"WARNING: Failed to load stored image {stored_path}", file=sys.stderr)
                continue

            if compare_faces(stored_img, live_img, cascade_path):
                email = os.path.splitext(filename)[0]
                return email

    return None

def main():
    # Default paths (modify these to match your environment)
    faces_dir = "C:/xampp/htdocs/faces"
    cascade_path = "C:/Users/yassi/OneDrive/Documents/GitHub/backup/PidevWorksphere/src/main/resources/haarcascade_frontalface_alt.xml"

    # Validate paths
    if not os.path.isdir(faces_dir):
        print(f"ERROR: Faces directory does not exist: {faces_dir}", file=sys.stderr)
        sys.exit(1)
    if not os.path.isfile(cascade_path):
        print(f"ERROR: Cascade file does not exist: {cascade_path}", file=sys.stderr)
        sys.exit(1)

    # Open webcam
    cap = cv2.VideoCapture(0)
    if not cap.isOpened():
        print("ERROR: Could not open webcam", file=sys.stderr)
        sys.exit(1)

    print("Webcam opened. Press 'c' to capture an image, 'q' to quit.")

    # Create a temporary directory for the captured image
    temp_dir = tempfile.mkdtemp()
    live_image_path = os.path.join(temp_dir, "live_face.png")

    try:
        while True:
            ret, frame = cap.read()
            if not ret:
                print("ERROR: Failed to capture frame", file=sys.stderr)
                break

            # Display the video feed
            cv2.imshow("Face Authentication Test", frame)

            # Check for key press
            key = cv2.waitKey(1) & 0xFF
            if key == ord('q'):
                print("Quitting...")
                break
            elif key == ord('c'):
                # Save the captured frame to a temporary file
                cv2.imwrite(live_image_path, frame)
                print(f"Captured image saved to: {live_image_path}")

                # Compare faces
                matched_email = compare_faces_with_directory(faces_dir, live_image_path, cascade_path)
                if matched_email:
                    print(f"Match found! Email: {matched_email}")
                else:
                    print("No match found.")

                # Remove the temporary file
                if os.path.exists(live_image_path):
                    os.remove(live_image_path)

    finally:
        # Clean up
        cap.release()
        cv2.destroyAllWindows()
        if os.path.exists(temp_dir):
            shutil.rmtree(temp_dir)
        print("Cleaned up resources.")

if __name__ == "__main__":
    main()
    