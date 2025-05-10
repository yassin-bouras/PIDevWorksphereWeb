<?php

namespace App\Controller;

use App\Entity\Offre;
use App\Form\OffreType;
use App\Repository\OffreRepository;
use App\Repository\CandidatureRepository;
use App\Repository\UserRepository;
use App\Service\CvAnalyzerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/offre')]
final class OffreController extends AbstractController
{
    private $jwtEncoder;
    private $userRepository;
    private $cvAnalyzerService;
    private $offreRepository;
    private $candidatureRepository;
    private $logger;
    
    public function __construct(
        JWTEncoderInterface $jwtEncoder, 
        UserRepository $userRepository, 
        CvAnalyzerService $cvAnalyzerService,
        OffreRepository $offreRepository,
        CandidatureRepository $candidatureRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->jwtEncoder = $jwtEncoder;
        $this->userRepository = $userRepository;
        $this->cvAnalyzerService = $cvAnalyzerService;
        $this->offreRepository = $offreRepository;
        $this->candidatureRepository = $candidatureRepository;
        $this->logger = $logger;
    }

    #[Route(name: 'app_offre_index', methods: ['GET'])]
    public function index(Request $request, OffreRepository $offreRepository): Response
    {
        $search = $request->query->get('search', '');
        $contractType = $request->query->get('contract_type', '');
        $sortBy = $request->query->get('sort_by', '');
        $offres = $offreRepository->findByTitre($search);
        $offres = $offreRepository->findBySearchAndContractType($search, $contractType);
        $offres = $offreRepository->findBySearchContractTypeAndSort($search, $contractType, $sortBy);


        return $this->render('offre/index.html.twig', [
            'offres' => $offres,
            'search' => $search,
            'contract_type' => $contractType,
            'sort_by' => $sortBy,
        ]);
    }

    #[Route('/statistics', name: 'app_offre_statistics', methods: ['GET'])]
    public function statistics(OffreRepository $offreRepository, ChartBuilderInterface $chartBuilder): Response
    {
        // Get all offers
        $offres = $offreRepository->findAll();

        // Initialize data arrays for charts
        $chartData = [
            'contractType' => [
                'data' => [],
                'labels' => [],
                'colors' => ['rgb(255, 99, 132)', 'rgb(54, 162, 235)', 'rgb(255, 205, 86)', 'rgb(75, 192, 192)'],
                'type' => 'doughnut'
            ],
            'location' => [
                'data' => [],
                'labels' => [],
                'colors' => ['rgb(153, 102, 255)', 'rgb(255, 159, 64)', 'rgb(201, 203, 207)', 'rgb(54, 162, 235)'],
                'type' => 'bar'
            ],
            'experience' => [
                'data' => [],
                'labels' => [],
                'colors' => ['rgb(75, 192, 192)', 'rgb(255, 99, 132)', 'rgb(54, 162, 235)', 'rgb(255, 205, 86)'],
                'type' => 'pie'
            ]
        ];

        // Temporary arrays to count occurrences
        $counts = [
            'contractTypes' => [],
            'locations' => [],
            'experiences' => []
        ];

        // Process data for charts
        foreach ($offres as $offre) {
            $contractType = $offre->getTypecontrat();
            $location = $offre->getLieutravail();
            $experience = $offre->getExperience();

            // Count occurrences
            if (!isset($counts['contractTypes'][$contractType])) {
                $counts['contractTypes'][$contractType] = 0;
            }
            $counts['contractTypes'][$contractType]++;

            if (!isset($counts['locations'][$location])) {
                $counts['locations'][$location] = 0;
            }
            $counts['locations'][$location]++;

            if (!isset($counts['experiences'][$experience])) {
                $counts['experiences'][$experience] = 0;
            }
            $counts['experiences'][$experience]++;
        }

        // Prepare data for charts
        foreach ($counts['contractTypes'] as $type => $count) {
            $chartData['contractType']['labels'][] = $type;
            $chartData['contractType']['data'][] = $count;
        }

        foreach ($counts['locations'] as $location => $count) {
            $chartData['location']['labels'][] = $location;
            $chartData['location']['data'][] = $count;
        }

        foreach ($counts['experiences'] as $experience => $count) {
            $chartData['experience']['labels'][] = $experience;
            $chartData['experience']['data'][] = $count;
        }

        // Create contract type chart (doughnut)
        $contractTypeChart = $chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $contractTypeChart->setData([
            'labels' => $chartData['contractType']['labels'],
            'datasets' => [
                [
                    'label' => 'Contract Types',
                    'backgroundColor' => $chartData['contractType']['colors'],
                    'borderColor' => 'rgb(255, 255, 255)',
                    'data' => $chartData['contractType']['data'],
                ],
            ],
        ]);
        $contractTypeChart->setOptions([
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Contract Types Distribution',
                ],
            ],
        ]);

        // Create location chart (bar)
        $locationChart = $chartBuilder->createChart(Chart::TYPE_BAR);
        $locationChart->setData([
            'labels' => $chartData['location']['labels'],
            'datasets' => [
                [
                    'label' => 'Job Locations',
                    'backgroundColor' => $chartData['location']['colors'],
                    'borderColor' => 'rgb(255, 255, 255)',
                    'data' => $chartData['location']['data'],
                ],
            ],
        ]);
        $locationChart->setOptions([
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Job Locations Distribution',
                ],
            ],
        ]);

        // Create experience chart (pie)
        $experienceChart = $chartBuilder->createChart(Chart::TYPE_PIE);
        $experienceChart->setData([
            'labels' => $chartData['experience']['labels'],
            'datasets' => [
                [
                    'label' => 'Experience Levels',
                    'backgroundColor' => $chartData['experience']['colors'],
                    'borderColor' => 'rgb(255, 255, 255)',
                    'data' => $chartData['experience']['data'],
                ],
            ],
        ]);
        $experienceChart->setOptions([
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Experience Levels Distribution',
                ],
            ],
        ]);

        // Create offers by date chart
        $dates = [];
        $offersByDate = [];

        // Group offers by month
        foreach ($offres as $offer) {
            $date = $offer->getDatepublication();
            if ($date) {
                $yearMonth = $date->format('Y-m');
                if (!isset($offersByDate[$yearMonth])) {
                    $offersByDate[$yearMonth] = 0;
                    $dates[] = $yearMonth;
                }
                $offersByDate[$yearMonth]++;
            }
        }

        // Sort dates chronologically
        sort($dates);

        // Prepare data array in the same order as sorted dates
        $dateData = [];
        foreach ($dates as $date) {
            $dateData[] = $offersByDate[$date];
        }

        // Create date chart (line)
        $dateChart = $chartBuilder->createChart(Chart::TYPE_LINE);
        $dateChart->setData([
            'labels' => $dates,
            'datasets' => [
                [
                    'label' => 'Offers Published',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'borderColor' => 'rgb(54, 162, 235)',
                    'data' => $dateData,
                    'tension' => 0.4,
                ],
            ],
        ]);
        $dateChart->setOptions([
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Offers Published by Month',
                ],
            ],
        ]);
        // dd($dateChart);
        return $this->render('offre/statistics.html.twig', [
            'contractTypeChart' => $contractTypeChart,
            'locationChart' => $locationChart,
            'experienceChart' => $experienceChart,
            'dateChart' => $dateChart
        ]);
    }
    #[Route('/front', name: 'app_offre_front_index', methods: ['GET'])]
    public function frontIndex(Request $request, OffreRepository $offreRepository, CandidatureRepository $candidatureRepository): Response
    {
        $search = $request->query->get('search', '');
        $contractType = $request->query->get('contract_type', '');
        $sortBy = $request->query->get('sort_by', '');
        $offres = $offreRepository->findByTitre($search);
        $offres = $offreRepository->findBySearchAndContractType($search, $contractType);
        $offres = $offreRepository->findBySearchContractTypeAndSort($search, $contractType, $sortBy);

        // Récupérer l'utilisateur actuel
        $currentUser = null;
        $userApplications = [];

        $token = $request->cookies->get('BEARER');
        if ($token && $this->jwtEncoder && $this->userRepository) {
            try {
                $decodedData = $this->jwtEncoder->decode($token);
                $email = $decodedData['username'] ?? null;

                if ($email) {
                    $currentUser = $this->userRepository->findOneBy(['email' => $email]);

                    // Pour chaque offre, vérifier si l'utilisateur a déjà postulé
                    if ($currentUser) {
                        foreach ($offres as $offre) {
                            $userApplications[$offre->getId_offre()] =
                                $candidatureRepository->hasUserAppliedToOffer($currentUser, $offre->getId_offre());
                        }
                    }
                }
            } catch (\Exception $e) {
                // Gérer l'erreur silencieusement
            }
        }

        return $this->render('offre/front_index.html.twig', [
            'offres' => $offres,
            'search' => $search,
            'contract_type' => $contractType,
            'sort_by' => $sortBy,
            'context' => 'front',
            'currentUser' => $currentUser,
            'userApplications' => $userApplications
        ]);
    }

    #[Route('/new', name: 'app_offre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, string $context = 'back'): Response
    {
        $offre = new Offre();
        $offre->setDatePublication(new \DateTime());

        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$offre->getDateLimite()) {
                $offre->setDateLimite(new \DateTime());
            }
            $entityManager->persist($offre);
            $entityManager->flush();

            return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offre/new.html.twig', [
            'offre' => $offre,
            'form' => $form,
            'context' => $context,
        ]);
    }

    #[Route('/{id_offre}', name: 'app_offre_show', methods: ['GET'])]
    public function show(Offre $offre, string $context = 'back'): Response
    {
        return $this->render('offre/show.html.twig', [
            'offre' => $offre,
            'context' => $context,
        ]);
    }

    #[Route('/{id_offre}/edit', name: 'app_offre_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Offre $offre, EntityManagerInterface $entityManager, string $context = 'back'): Response
    {
        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute($context === 'front' ? 'app_offre_front_index' : 'app_offre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('offre/edit.html.twig', [
            'offre' => $offre,
            'form' => $form,
            'context' => $context,
        ]);
    }

    #[Route('/{id_offre}', name: 'app_offre_delete', methods: ['POST'])]
    public function delete(Request $request, Offre $offre, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $offre->getId_offre(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($offre);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_offre_index', [], Response::HTTP_SEE_OTHER);
    }

    // #[Route('/{id_offre}/candidatures', name: 'app_offre_candidatures', methods: ['GET'])]
    // public function candidatures(Offre $offre, CandidatureRepository $candidatureRepository): Response
    // {
    //     $candidatures = $candidatureRepository->findBy(['offre' => $offre]);

    //     return $this->render('offre/candidatures.html.twig', [
    //         'offre' => $offre,
    //         'candidatures' => $candidatures,
    //     ]);
    // }

    #[Route('/{id_offre}/compare-cv/{id_candidature}', name: 'app_offre_compare_cv', methods: ['GET'])]
    public function compareCv(Offre $offre, $id_candidature, EntityManagerInterface $entityManager): Response
    {
        $candidature = $entityManager->getRepository('App\Entity\Candidature')->find($id_candidature);

        if (!$candidature) {
            throw $this->createNotFoundException('Candidature not found');
        }

        // Get offer description
        $offreDescription = $offre->getDescription();

        // Use both description and experience for a more comprehensive comparison
        $jobRequirements = $offre->getExperience();

        // Get candidate CV path
        $cvPath = $candidature->getCv();
        $fullCvPath = $this->getParameter('uploads_directory') . '/' . $cvPath;

        $analysisResult = [];

        if (file_exists($fullCvPath) && is_readable($fullCvPath)) {
            // Extract text from the PDF CV
            $cvContent = $this->cvAnalyzerService->extractTextFromPdf($fullCvPath);
            // Compare CV with job requirements using AI and include both description and experience
            $analysisResult = $this->cvAnalyzerService->compareCvWithJobRequirements(
                $cvContent,
                $offreDescription,
                $jobRequirements
            );
        } else {
            // Fallback if CV file not found or not readable
            $analysisResult = [
                'matchPercentage' => 0,
                'matchedSkills' => [],
                'missingSkills' => [],
                'assessment' => 'CV file not found or not readable'
            ];
        }

        return $this->render('offre/compare_cv.html.twig', [
            'offre' => $offre,
            'candidature' => $candidature,
            'matchPercentage' => $analysisResult['matchPercentage'],
            'matchedSkills' => $analysisResult['matchedSkills'],
            'missingSkills' => $analysisResult['missingSkills'],
            'assessment' => $analysisResult['assessment'] ?? 'No assessment available',
            'cvPath' => $cvPath
        ]);
    }

    #[Route('/{id_offre}/translate-cv/{id_candidature}', name: 'app_offre_translate_cv', methods: ['GET', 'POST'])]
    public function translateCv(Request $request, Offre $offre, $id_candidature, EntityManagerInterface $entityManager): Response
    {
        $candidature = $entityManager->getRepository('App\Entity\Candidature')->find($id_candidature);

        if (!$candidature) {
            throw $this->createNotFoundException('Candidature not found');
        }

        // Get candidate CV path
        $cvPath = $candidature->getCv();
        $fullCvPath = $this->getParameter('uploads_directory') . '/' . $cvPath;

        $targetLanguage = $request->query->get('language', '');
        $translatedContent = '';
        $error = null;
        $success = false;

        // Only proceed if a language is specified
        if ($request->isMethod('POST') && $request->request->has('language')) {
            $targetLanguage = $request->request->get('language');

            if (file_exists($fullCvPath) && is_readable($fullCvPath)) {
                // Extract text from the PDF CV
                $cvContent = $this->cvAnalyzerService->extractTextFromPdf($fullCvPath);

                // Translate CV to target language
                $result = $this->cvAnalyzerService->translateCv($cvContent, $targetLanguage);

                if ($result['success']) {
                    $translatedContent = $result['translatedContent'];
                    $success = true;
                } else {
                    $error = $result['message'];
                }
            } else {
                $error = 'CV file not found or not readable';
            }
        }

        return $this->render('offre/translate_cv.html.twig', [
            'offre' => $offre,
            'candidature' => $candidature,
            'cvPath' => $cvPath,
            'translatedContent' => $translatedContent,
            'targetLanguage' => $targetLanguage,
            'error' => $error,
            'success' => $success,
            'languages' => [
                'English' => 'English',
                'French' => 'Français',
                'Spanish' => 'Español',
                'German' => 'Deutsch',
                'Italian' => 'Italiano',
                'Arabic' => 'العربية',
                'Chinese' => '中文',
                'Japanese' => '日本語',
                'Russian' => 'Русский',
                'Portuguese' => 'Português'
            ]
        ]);
    }



    /**
     * @Route("/candidatures/{id_offre}", name="app_offre_candidatures", methods={"GET"})
     */
    #[Route('/{id_offre}/candidatures', name: 'app_offre_candidatures', methods: ['GET'])]
    public function candidatures(int $id_offre, CvAnalyzerService $cvAnalyzerService): Response
    {
        $offre = $this->offreRepository->find($id_offre);

        if (!$offre) {
            throw $this->createNotFoundException('Offre non trouvée');
        }

        $candidatures = $this->candidatureRepository->findBy(['offre' => $offre]);

        // Add match percentages to candidatures
        foreach ($candidatures as $candidature) {
            // Default to 0 if no CV
            $matchPercentage = 0;

            // Only calculate if CV exists
            if ($candidature->getCv()) {
                $cvPath = $this->getParameter('uploads_directory') . '/' . $candidature->getCv();

                if (file_exists($cvPath)) {
                    try {
                        // Extract text from CV and compare with job requirements
                        $cvContent = $cvAnalyzerService->extractTextFromPdf($cvPath);
                        $result = $cvAnalyzerService->compareCvWithJobRequirements(
                            $cvContent,
                            $offre->getDescription(),
                            $offre->getExperience()
                        );

                        // Store match percentage in candidature object (temporary, not saved to DB)
                        $matchPercentage = $result['matchPercentage'] ?? 0;
                    } catch (\Exception $e) {
                        // Log error but continue processing
                        $this->logger->error('Error analyzing CV: ' . $e->getMessage());
                    }
                }
            }

            // Attach the match percentage to the candidature object
            $candidature->matchPercentage = $matchPercentage;
        }

        return $this->render('offre/candidatures.html.twig', [
            'offre' => $offre,
            'candidatures' => $candidatures,
        ]);
    }
}
