import sys
import os
import cv2

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

def main():
    if len(sys.argv) != 4:
        print("Usage: python face_compare.py <faces_dir> <live_image_path> <cascade_path>", file=sys.stderr)
        sys.exit(1)

    faces_dir = sys.argv[1]
    live_image_path = sys.argv[2]
    cascade_path = sys.argv[3]

    if not os.path.isdir(faces_dir):
        print("ERROR: Faces directory not found", file=sys.stderr)
        sys.exit(1)

    if not os.path.isfile(live_image_path):
        print("ERROR: Live image not found", file=sys.stderr)
        sys.exit(1)

    live_img = cv2.imread(live_image_path)
    if live_img is None:
        print("ERROR: Failed to load live image", file=sys.stderr)
        sys.exit(1)

    for filename in os.listdir(faces_dir):
        if filename.lower().endswith(('.png', '.jpg', '.jpeg')):
            stored_path = os.path.join(faces_dir, filename)
            stored_img = cv2.imread(stored_path)

            if stored_img is None:
                continue

            if compare_faces(stored_img, live_img, cascade_path):
                email = os.path.splitext(filename)[0]
                print(email)  # Important: print matched email
                return

    print("")  # No match found

if __name__ == "__main__":
    main()
