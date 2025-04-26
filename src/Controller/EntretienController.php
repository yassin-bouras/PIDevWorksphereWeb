<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Entity\Entretien;
use App\Entity\Offre;
use App\Entity\User;
use App\Form\EntretienType;
use App\Repository\CandidatureRepository;
use App\Repository\EntretienRepository;
use App\Repository\UserRepository;
use App\Service\GeminiService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpKernel\KernelInterface; 



#[Route('/entretien')]
final class EntretienController extends AbstractController
{


    private $jwtEncoder;
    private $userRepository;

    private KernelInterface $kernel;


    private $pdfGenerator;


    public function __construct(JWTEncoderInterface $jwtEncoder, UserRepository $userRepository , Pdf $pdfGenerator , KernelInterface $kernel)
    {
        $this->jwtEncoder = $jwtEncoder;
        $this->userRepository = $userRepository;
        $this->pdfGenerator = $pdfGenerator;
        $this->kernel = $kernel;


    }

    #[Route(name: 'app_entretien_index', methods: ['GET'])]
    public function index(
        EntretienRepository $entretienRepository,
        PaginatorInterface $paginator,
        Request $request
            ): Response {
        $query = $entretienRepository->createQueryBuilder('e')
            ->orderBy('e.date_entretien', 'DESC')
            ->getQuery();

        $entretiens = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), 
            2
        );

        return $this->render('entretien/index.html.twig', [
            'entretiens' => $entretiens,
        ]);
    }







    #[Route('/employee', name: 'entretien_by_employee')]
    public function showEntretienByEmployee(Request $request, EntretienRepository $entretienRepository): Response
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

            $entretiens = $entretienRepository->findByEmployeeId($user->getIduser());

            return $this->render('entretien/index2.html.twig', [
                'entretiens' => $entretiens,
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Token invalide ou expiré.');
            return $this->redirectToRoute('app_login');
        }
    }


    #[Route('/employees', name: 'entretien_by_employees')]
    public function showEntretienByEmployees(Request $request, EntretienRepository $entretienRepository): Response
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

            $entretiens = $entretienRepository->findByEmployeeId($user->getIduser());

            return $this->render('entretien/entretienArchive.html.twig', [
                'entretiens' => $entretiens,
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Token invalide ou expiré.');
            return $this->redirectToRoute('app_login');
        }
    }




    #[Route('/new', name: 'app_entretien_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, CandidatureRepository $candidatureRepo): Response
    {
        $entretien = new Entretien();
        $candidature = new Candidature();

        $entretien->setStatus(false);

        $form = $this->createForm(EntretienType::class, $entretien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($entretien);
            $entityManager->flush();

            $offre = $entretien->getOffre();
            $candidatId = $entretien->getCandidatId();

            if (!$offre || !$candidatId) {
                $this->addFlash('warning', '⚠ Offre ou Candidat non défini pour cet entretien.');
            }

            $candidature = $candidatureRepo->findOneBy([
                'offre' => $offre,
                'user' => $candidatId
            ]);

            if (!$candidature) {
                $this->addFlash('warning', '⚠ Aucune candidature trouvée pour cette offre et ce candidat !');
            }

            $entretien->setCandidature($candidature);

            $entityManager->persist($entretien);
            $entityManager->flush();


            $this->addFlash('success', '✅ Entretien ajouté avec succès !');
            return $this->redirectToRoute('app_entretien_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('entretien/new.html.twig', [
            'entretien' => $entretien,
            'form' => $form,
        ]);
    }





    #[Route('/{id}', name: 'app_entretien_show', requirements: ['id' => '\d+'], methods: ['GET'])]

    public function show(int $id, Entretien $entretien, EntityManagerInterface $em, EntretienRepository $entretienRepository): Response
    {
        $candidat = null;
        $entretien = $entretienRepository->find($id);

        if (!$entretien) {
            $this->addFlash('error', 'Entretien non trouvé');
            return $this->redirectToRoute('app_entretien_index');
        }

        $candidatId = $entretien->getCandidatId();

        if ($candidatId !== null) {
            $candidat = $em->getRepository(User::class)->find($candidatId);
        }

        return $this->render('entretien/show.html.twig', [
            'entretien' => $entretien,
            'candidat' => $candidat,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_entretien_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Entretien $entretien, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EntretienType::class, $entretien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_entretien_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('entretien/edit.html.twig', [
            'entretien' => $entretien,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_entretien_delete', methods: ['POST'])]
    public function delete(Request $request, Entretien $entretien, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $entretien->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($entretien);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_entretien_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/candidats/sans-entretien/{idOffre}', name: 'app_candidats_sans_entretien')]
    public function getCandidatsSansEntretien(int $idOffre, EntityManagerInterface $entityManager): JsonResponse
    {
        $offre = $entityManager->getRepository(Offre::class)->find($idOffre);
        if (!$offre) {
            return new JsonResponse(['error' => 'Offre non trouvée'], 404);
        }

        $candidatures = $entityManager->getRepository(Candidature::class)
            ->findBy(['offre' => $offre]);

        $entretiens = $entityManager->getRepository(Entretien::class)
            ->findBy(['offre' => $offre]);

        $candidatsAyantPostule = array_map(fn($c) => $c->getUser()->getId(), $candidatures);
        $candidatsAvecEntretien = array_map(fn($e) => $e->getCandidatId(), $entretiens);

        $candidatsSansEntretienIds = array_diff($candidatsAyantPostule, $candidatsAvecEntretien);

        $candidats = $entityManager->getRepository(User::class)
            ->findBy(['iduser' => $candidatsSansEntretienIds]);

        $result = array_map(function (User $user) {
            return [
                'iduser' => $user->getId(),
                'nom' => $user->getNom(),
                'prenom' => $user->getPrenom()
            ];
        }, $candidats);

        return new JsonResponse($result);
    }


    #[Route('/change-status/{id}', name: 'entretien_change_status', methods: ['POST'])]
    public function changeStatus(int $id, EntityManagerInterface $entityManager, Request $request, EntretienRepository $entretienRepository)
    {
        $entretien = $entityManager->getRepository(Entretien::class)->find($id);

        if (!$entretien) {
            return new JsonResponse(['error' => 'Entretien non trouvé.'], 404);
        }

        $entretien->setStatus(true);

        $entityManager->flush();

        $token = $request->cookies->get('BEARER');

        if (!$token) {
            $this->redirectToRoute('app_login');
        }

        try {
            $decodedData = $this->jwtEncoder->decode($token);
            $email = $decodedData['username'] ?? null;

            if (!$email) {
                $this->addFlash('error', 'Email non trouvé dans le token.');
                $this->redirectToRoute('app_login');
            }

            $user = $this->userRepository->findOneBy(['email' => $email]);

            if (!$user) {
                $this->addFlash('error', 'Utilisateur non trouvé.');
                $this->redirectToRoute('app_login');
            }

            $entretiens = $entretienRepository->findByEmployeeIdWithStatusTrue($user->getIduser());

            return $this->render('entretien/entretienArchive.html.twig', [
                'entretiens' => $entretiens,
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Token invalide ou expiré.');
            $this->redirectToRoute('app_login');
        }
    }


    #[Route('/entretiens/filtrer', name: 'filtrer_entretiens')]
public function filtrerParDate(
    Request $request,
    EntretienRepository $entretienRepository,
    PaginatorInterface $paginator
) {
    $startDate = $request->query->get('start_date');
    $endDate = $request->query->get('end_date');

    // Création de la requête de base
    $queryBuilder = $entretienRepository->createQueryBuilder('e')
        ->orderBy('e.date_entretien', 'DESC');

    // Application des filtres si les dates sont fournies
    if ($startDate && $endDate) {
        $startDateTime = new \DateTime($startDate);
        $endDateTime = new \DateTime($endDate);

        $queryBuilder
            ->andWhere('e.date_entretien BETWEEN :start AND :end')
            ->setParameter('start', $startDateTime)
            ->setParameter('end', $endDateTime);
    }

    // Pagination des résultats
    $entretiens = $paginator->paginate(
        $queryBuilder->getQuery(), // Requête Doctrine
        $request->query->getInt('page', 1), // Numéro de page
        2 // Nombre d'éléments par page
    );

    return $this->render('entretien/index.html.twig', [
        'entretiens' => $entretiens,
        'start_date' => $startDate, // Conserver les dates pour le formulaire
        'end_date' => $endDate
    ]);
}


    #[Route('/entretienssss/filtrers', name: 'filtrer_entretiens_keyWords', methods: ['GET'])]
    public function filtrerParKeyword(Request $request, EntretienRepository $entretienRepository)
    {
        $search = $request->query->get('search', '');

        if ($search) {
            $entretiens = $entretienRepository->findByKeyword($search);
        } else {
            $entretiens = $entretienRepository->findAll();
        }

        return $this->render('entretien/index.html.twig', [
            'entretiens' => $entretiens,
        ]);
    }


    #[Route('/calendar', name: 'entretien_calendar', methods: ['GET'])]
    public function calendar(): Response
    {
        return $this->render('entretien/calendar.html.twig');
    }


    #[Route('/api/entretiens', name: 'api_entretiens', methods: ['GET'])]
    public function getEntretiensForCalendar(EntretienRepository $entretienRepository, Request $request): JsonResponse
    {
        $token = $request->cookies->get('BEARER');

        if (!$token) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        try {
            $decodedData = $this->jwtEncoder->decode($token);
            $email = $decodedData['username'] ?? null;
            $user = $this->userRepository->findOneBy(['email' => $email]);

            if (!$user) {
                return new JsonResponse(['error' => 'User not found'], 404);
            }

            $entretiens = $entretienRepository->findByEmployeeId($user->getIduser());
            $events = [];
            foreach ($entretiens as $entretien) {
                $dateEntretien = $entretien->getDate_entretien();
                $heureDebut = $entretien->getHeureentretien();
                $heureFin = $entretien->getHeureentretien();

                $events[] = [
                    'title' => 'Entretien ' . $entretien->getTitre(),
                    'start' => $dateEntretien->format('Y-m-d H:i:s'),
                    'end' => (clone $dateEntretien)->modify('+1 hour')->format('Y-m-d H:i:s'),
                    'url' => $this->generateUrl('app_entretien_show', ['id' => $entretien->getId()]),
                    'backgroundColor' => $entretien->isStatus() ? '#28a745' : '#007bff',
                    'borderColor' => $entretien->isStatus() ? '#28a745' : '#007bff',
                    'extendedProps' => [
                        'description' => $entretien->getDescription(),
                        'offre' => $entretien->getOffre()->getTitre(),
                        'heure' => $entretien->getHeureentretien(),
                        'status' => $entretien->isStatus() ? 'Terminé' : 'Planifié'
                    ]
                ];
            }
            return new JsonResponse($events);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Invalid token'], 401);
        }
    }

    #[Route('/generate-questions/{id}', name: 'app_entretien_generate_questions', requirements: ['id' => '\d+'], methods: ['GET'])]
public function generateQuestions(
    int $id,
    GeminiService $geminiService,
    EntretienRepository $entretienRepository
): Response {
    $entretien = $entretienRepository->find($id);

    if (!$entretien) {
        $this->addFlash('error', 'Entretien non trouvé');
        return $this->redirectToRoute('app_entretien_index');
    }

    $questions = $geminiService->generateInterviewQuestions($entretien->getTitre());

    if (!$questions) {
        $this->addFlash('error', "Impossible de générer les questions");
        return $this->redirectToRoute('app_entretien_show', ['id' => $id]);
    }

    // Pas de try-catch ici pour voir directement l'erreur
    $filename = 'questions_entretien_'.$id.'.pdf';
    $pdfPath = $geminiService->generatePdfFromQuestions($questions, $filename);

    // Ajoute un contrôle d'existence de fichier
    $fullPath = $this->getParameter('kernel.project_dir').'/public'.$pdfPath;

    if (!file_exists($fullPath)) {
        throw new \Exception('Le fichier PDF n\'a pas été généré: '.$fullPath);
    }

    $pdfContent = file_get_contents($fullPath);

    if ($pdfContent === false) {
        throw new \Exception('Impossible de lire le contenu du fichier PDF généré.');
    }

    return new Response(
        $pdfContent,
        Response::HTTP_OK,
        [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf('inline; filename="%s"', $filename)
        ]
    );
}



   



#[Route('/test/generate-questions', name: 'test_generate_questions', methods: ['GET'])]
public function testGenerateQuestions(Request $request, GeminiService $geminiService): Response
{
    $poste = $request->query->get('poste', 'Développeur');

    $questions = $geminiService->generateInterviewQuestions($poste);

    if (!$questions) {
        return $this->json(['error' => 'Impossible de générer les questions'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    return $this->json([
        'poste' => $poste,
        'questions' => $questions,
    ]);
}
}


















    // #[Route('/filtrer-par-date', name: 'entretien_filtrer_par_date', methods: ['GET'])]
    // public function filtrerParDate(Request $request, EntretienRepository $entretienRepository): Response
    // {
    //     $dateDebutStr = $request->query->get('date_debut');
    //     $dateFinStr = $request->query->get('date_fin');

    //     if (!$dateDebutStr || !$dateFinStr) {
    //         $this->addFlash('error', 'Veuillez fournir les deux dates.');
    //         return $this->redirectToRoute('app_entretien_index');
    //     }

    //     try {
    //         $dateDebut = new \DateTime($dateDebutStr);
    //         $dateFin = new \DateTime($dateFinStr);
    //     } catch (\Exception $e) {
    //         $this->addFlash('error', 'Format de date invalide.');
    //         return $this->redirectToRoute('app_entretien_index');
    //     }

    //     $entretiens = $entretienRepository->findByDateRange($dateDebut, $dateFin);

    //     return $this->render('entretien/index.html.twig', [
    //         'entretiens' => $entretiens,
    //     ]);
    // }










