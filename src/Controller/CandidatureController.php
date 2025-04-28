<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Entity\Offre;  // Add this import
use App\Form\CandidatureType;
use App\Entity\User;
use App\Entity\Notification;
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

    #[Route('/', name: 'app_candidature_index', methods: ['GET'])]
    public function index(Request $request, CandidatureRepository $candidatureRepository): Response
    {
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

            $search = $request->query->get('search', '');
            $candidatures = $candidatureRepository->findByUserAndOffreTitre($user, $search);

            return $this->render('candidature/index.html.twig', [
                'candidatures' => $candidatures,
                'search' => $search,
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors du décodage du token.');
            return $this->redirectToRoute('app_login');
        }
    }

    #[Route('/new/{offre_id}', name: 'app_candidature_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, CandidatureRepository $candidatureRepository, ?int $offre_id = null): Response
    {

        $candidature = new Candidature();
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

            // Vérifier si l'utilisateur a déjà postulé à cette offre
            if ($offre_id && $candidatureRepository->hasUserAppliedToOffer($user, $offre_id)) {
                $this->addFlash('error', 'Vous avez déjà postulé à cette offre.');
                return $this->redirectToRoute('app_offre_front_index');
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
                }

                $candidature->setCv($newFilename);
            }

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
            // Get the offer ID before removing the candidature
            $offreId = null;
            $user = $candidature->getUser();
            $offreTitre = $candidature->getOffre() ? $candidature->getOffre()->getTitre() : 'N/A';
            if ($candidature->getOffre()) {
                $offreId = $candidature->getOffre()->getId_offre(); // Use getId_offre instead of getId
                $offreTitre = $candidature->getOffre()->getTitre();

            }
            // Create notification for the candidate
            if ($user) {
                $notification = new Notification();
                $notification->setUser($user);
                $notification->setMessage("Votre candidature pour l'offre \"$offreTitre\" a été supprimée par le recruteur, à cause du fait qu'elle ne respectait pas nos condidtions.\n\nCordialement,\nLe Service Recrutement.");
                $notification->setCreatedAt(new \DateTime());
                $notification->setIsRead(false);
                $notification->setNotificationType('candidature_deleted');

                // Persist notification
                $entityManager->persist($notification);
            }

            $entityManager->remove($candidature);
            $entityManager->flush();

            // If we have the offer ID, redirect back to the candidatures page for that offer
            if ($offreId) {
                $this->addFlash('success', 'Candidature supprimée avec succès.');
                return $this->redirectToRoute('app_offre_candidatures', ['id_offre' => $offreId]); // Use id_offre instead of id
            }
        }

        return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
    }
}
