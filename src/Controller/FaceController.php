<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use App\Repository\UserRepository;

class FaceController extends AbstractController
{
    private $jwtManager;

    public function __construct(JWTTokenManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    #[Route('/face-auth', name: 'face_auth', methods: ['POST'])]
    public function authenticateWithFace(
        Request $request,
        UserRepository $userRepository,
        JWTTokenManagerInterface $jwtManager
    ): JsonResponse {
        error_log('DEBUG: Entering /face-auth route');

        $data = json_decode($request->getContent(), true);

        if (!isset($data['image'])) {
            error_log('ERROR: No image provided.');
            return new JsonResponse(['error' => 'No image provided.'], 400);
        }

        $imageData = $data['image'];

        // Decode base64
        $imageData = preg_replace('/^data:image\/(png|jpeg);base64,/', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        $decodedImage = base64_decode($imageData, true);

        if ($decodedImage === false) {
            error_log('ERROR: Failed to decode image.');
            return new JsonResponse(['error' => 'Failed to decode image.'], 400);
        }

        // Save temporary live photo
        $tempDir = sys_get_temp_dir();
        $liveCapturePath = $tempDir . '/live_face_' . uniqid() . '.png';
        if (!file_put_contents($liveCapturePath, $decodedImage) || !file_exists($liveCapturePath)) {
            error_log('ERROR: Failed to save captured image to: ' . $liveCapturePath);
            return new JsonResponse(['error' => 'Failed to save captured image.'], 500);
        }

        // Paths (aligned with Java)
        $facesDirectory = realpath('C:/xampp/htdocs/faces');
        $cascadePath = realpath('C:/Users/yassi/OneDrive/Documents/GitHub/backup/PidevWorksphere/src/main/resources/haarcascade_frontalface_alt.xml');
        $pythonScript = realpath('C:/Users/yassi/OneDrive/Documents/GitHub/finalone/safeone/PIDevWorksphereWeb/public/script/face_compare.py');
        $pythonExe = 'C:/BD/Python/python.exe';

        // Validate paths
        if (!$facesDirectory || !is_dir($facesDirectory) || !is_readable($facesDirectory)) {
            error_log('ERROR: Faces directory invalid: ' . $facesDirectory);
            return new JsonResponse(['error' => 'Faces directory not found.', 'debug' => 'Directory missing'], 500);
        }
        if (!$cascadePath || !file_exists($cascadePath) || !is_readable($cascadePath)) {
            error_log('ERROR: Cascade file invalid: ' . $cascadePath);
            return new JsonResponse(['error' => 'Face cascade not found.', 'debug' => 'Cascade file missing'], 500);
        }
        if (!$pythonScript || !file_exists($pythonScript) || !is_readable($pythonScript)) {
            error_log('ERROR: Python script invalid: ' . $pythonScript);
            return new JsonResponse(['error' => 'Python script not found.', 'debug' => 'Script missing'], 500);
        }
        if (!file_exists($pythonExe)) {
            error_log('ERROR: Python executable not found: ' . $pythonExe);
            return new JsonResponse(['error' => 'Python executable not found.', 'debug' => 'Python missing'], 500);
        }

        try {
            // Compare faces using Python
            $matchedEmail = $this->compareFaces($facesDirectory, $liveCapturePath, $cascadePath);

            if (!$matchedEmail) {
                error_log('DEBUG: No matching face found.');
                return new JsonResponse(['error' => 'No matching face found.'], 403);
            }

            error_log('DEBUG: Matched email: ' . $matchedEmail);

            // Find User by email
            $user = $userRepository->findOneBy(['email' => $matchedEmail]);

            if (!$user) {
                error_log('ERROR: User not found for email: ' . $matchedEmail);
                return new JsonResponse([
                    'error' => 'User not found for matched face.',
                    'matched_email' => $matchedEmail
                ], 404);
            }

            if ($user->isBanned()) {
                error_log('DEBUG: User is banned: ' . $matchedEmail);
                return new JsonResponse([
                    'error' => 'User is banned.',
                    'reclamation' => $user->getMessageReclamation() ?? null,
                ], 403);
            }

            // Generate JWT
            $jwt = $jwtManager->create($user);
            error_log('DEBUG: JWT generated for user: ' . $matchedEmail);

            return new JsonResponse([
                'message' => 'Authentication successful.',
                'token' => $jwt,
                'role' => $user->getRoles()[0] ?? 'ROLE_USER'
            ]);
        } finally {
            // Clean up temporary file
            if (file_exists($liveCapturePath)) {
                @unlink($liveCapturePath);
                error_log('DEBUG: Cleaned up temporary file: ' . $liveCapturePath);
            }
        }
    }

    private function compareFaces(string $facesDir, string $liveImagePath, string $cascadePath): ?string
    {
        // Execute Python script to handle face comparison
        $pythonExe = 'C:/BD/Python/python.exe';
        $command = escapeshellcmd(sprintf(
            '%s %s %s %s %s',
            $pythonExe,
            escapeshellarg('C:/Users/yassi/OneDrive/Documents/GitHub/finalone/safeone/PIDevWorksphereWeb/public/script/face_compare.py'),
            escapeshellarg($facesDir),
            escapeshellarg($liveImagePath),
            escapeshellarg($cascadePath)
        ));

        $output = [];
        $returnVar = 0;
        exec($command . ' 2>&1', $output, $returnVar);
        error_log('DEBUG: Python script command: ' . $command);
        error_log('DEBUG: Python script return code: ' . $returnVar);
        error_log('DEBUG: Python script output: ' . implode("\n", $output));

        if ($returnVar !== 0) {
            error_log('ERROR: Python script failed with code ' . $returnVar);
            return null;
        }

        $matchedEmail = trim(implode('', $output));
        return $matchedEmail !== '' ? $matchedEmail : null;
    }

    #[Route('/upload-face', name: 'app_upload_face', methods: ['POST'])]
    public function uploadFace(Request $request): Response
    {
        $email = $request->request->get('email');
        $imageData = $request->request->get('image');

        if (!$email || !$imageData) {
            error_log('ERROR: Missing email or image data.');
            return new JsonResponse(['message' => 'Invalid data.', 'debug' => 'Missing fields'], 400);
        }

        // Decode base64 image
        $imageData = preg_replace('/^data:image\/(png|jpeg);base64,/', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);
        $decodedImage = base64_decode($imageData, true);
        if ($decodedImage === false) {
            error_log('ERROR: Base64 decoding failed.');
            return new JsonResponse(['message' => 'Failed to decode image.', 'debug' => 'Base64 decode error'], 400);
        }

        // Save image
        $saveDirectory = realpath('C:/xampp/htdocs/faces');
        if (!$saveDirectory || !is_dir($saveDirectory)) {
            mkdir('C:/xampp/htdocs/faces', 0777, true);
        }
        if (!is_writable($saveDirectory)) {
            error_log('ERROR: Faces directory not writable: ' . $saveDirectory);
            return new JsonResponse(['message' => 'Cannot write to faces directory.', 'debug' => 'Directory permissions'], 500);
        }

        $safeEmail = preg_replace('/[^a-zA-Z0-9-_\.]/', '@', $email);
        $filename = $saveDirectory . '/' . $safeEmail . '.png';
        if (!file_put_contents($filename, $decodedImage)) {
            error_log('ERROR: Failed to save face image: ' . $filename);
            return new JsonResponse(['message' => 'Failed to save face.', 'debug' => 'File write error'], 500);
        }

        error_log('DEBUG: Face registered successfully for: ' . $email);
        return new JsonResponse(['message' => 'Face registered successfully.'], 200);
    }
}
