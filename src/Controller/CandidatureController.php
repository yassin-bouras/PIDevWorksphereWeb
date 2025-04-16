<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Entity\Offre;  // Add this import
use App\Form\CandidatureType;
use App\Entity\User;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use App\Repository\CandidatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/candidature')]
final class CandidatureController extends AbstractController
{

    private $jwtEncoder;
    private $userRepository;


    public function __construct(JWTEncoderInterface $jwtEncoder, UserRepository $userRepository)
    {
        $this->jwtEncoder = $jwtEncoder;
        $this->userRepository = $userRepository;
    }

    #[Route(name: 'app_candidature_index', methods: ['GET'])]
    public function index(Request $request, CandidatureRepository $candidatureRepository): Response
    {
        $search = $request->query->get('search', ''); // Get the search term from the query string
        $candidatures = $candidatureRepository->findByOffreTitre($search);

        return $this->render('candidature/index.html.twig', [
            // 'candidatures' => $candidatureRepository->findAll(),
            'candidatures' => $candidatures,
            'search' => $search, // Pass the search term back to the template    
        ]);
    }

    #[Route('/new/{offre_id}', name: 'app_candidature_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, ?int $offre_id = null): Response
    {
        
        $candidature = new Candidature();
        // $candidature->setUser($this->getUser());
        $token = $request->cookies->get('BEARER');

        if (!$token) {
            return $this->redirectToRoute('app_login');
        }

        try {
            $decodedData = $this->jwtEncoder->decode($token);
            $email = $decodedData['username'] ?? null;

            if (!$email) {
                $this->addFlash('error', 'Email non trouvé dans le token.');
                return $this->redirectToRoute('app_login');
            }

            $user = $this->userRepository->findOneBy(['email' => $email]);

            if (!$user) {
                $this->addFlash('error', 'Utilisateur non trouvé.');
                return $this->redirectToRoute('app_login');
            }

            $candidature->setUser($user);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la décodage du token.');
            return $this->redirectToRoute('app_login');
        }

        if ($offre_id) {
            $offre = $entityManager->getRepository(Offre::class)->find($offre_id);
            if ($offre) {
                $candidature->setOffre($offre);
            }
        }

        $form = $this->createForm(CandidatureType::class, $candidature, [
            'offre_id' => $offre_id
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // CV Upload
            $cvFile = $form->get('cv')->getData();
            if ($cvFile) {
                $originalFilename = pathinfo($cvFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $cvFile->guessExtension();

                try {
                    $cvFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle error here
                }

                $candidature->setCv($newFilename);
            }

            // Lettre de Motivation Upload
            $lmFile = $form->get('lettre_motivation')->getData();
            if ($lmFile) {
                $originalFilename = pathinfo($lmFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $lmFile->guessExtension();

                try {
                    $lmFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle error here
                }

                $candidature->setLettreMotivation($newFilename);
            }
            $entityManager->persist($candidature);
            $entityManager->flush();

            return $this->redirectToRoute('app_candidature_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('candidature/new.html.twig', [
            'candidature' => $candidature,
            'form' => $form,
        ]);
    }

    #[Route('/{id_candidature}', name: 'app_candidature_show', methods: ['GET'])]
    public function show(Candidature $candidature): Response
    {
        return $this->render('candidature/show.html.twig', [
            'candidature' => $candidature,
        ]);
    }

    #[Route('/{id_candidature}/edit', name: 'app_candidature_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Candidature $candidature, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CandidatureType::class, $candidature);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_candidature_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('candidature/edit.html.twig', [
            'candidature' => $candidature,
            'form' => $form,
        ]);
    }

    #[Route('/{id_candidature}', name: 'app_candidature_delete', methods: ['POST'])]
    public function delete(Request $request, Candidature $candidature, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $candidature->getId_candidature(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($candidature);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_candidature_index', [], Response::HTTP_SEE_OTHER);
    }

}
