<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Form\ProjetType;
use App\Repository\ProjetRepository;
use App\Repository\EquipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\PdfGeneratorService;
use App\Service\CloudinaryUploader;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\Component\Pager\PaginatorInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\KernelInterface; 

#[Route('/projet')]
final class ProjetController extends AbstractController{

    private $jwtEncoder;
    private $userRepository;

    public function __construct(JWTEncoderInterface $jwtEncoder, UserRepository $userRepository)
    {
        $this->jwtEncoder = $jwtEncoder;
        $this->userRepository = $userRepository;
     

    }
 

    #[Route(name: 'app_projet_index', methods: ['GET'])]
    public function index(ProjetRepository $projetRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $token = $request->cookies->get('BEARER');
    
        if (!$token) {
            return $this->redirectToRoute('app_login');
        }
    
        try {
            $decodedData = $this->jwtEncoder->decode($token);
            $email = $decodedData['username'] ?? null;
            $user = $this->userRepository->findOneBy(['email' => $email]);
    
            if (!$user) {
                return $this->redirectToRoute('app_login');
            }
    
            $nom = $request->query->get('search');
            $etat = $request->query->get('etat');
            $nomEquipe = $request->query->get('nomEquipe');
    
           
            $query = $projetRepository->findProjectsByUser($user->getIduser(), $nom, $etat, $nomEquipe);
    
            $projets = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                2
            );
    
            return $this->render('projet/index.html.twig', [
                'projets' => $projets,
            ]);
    
        } catch (\Exception $e) {
            $this->addFlash('error', 'Token invalide ou expiré.');
            return $this->redirectToRoute('app_login');
        }
    }
    


#[Route('/new', name: 'app_projet_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
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

        $projet = new Projet();
        $form = $this->createForm(ProjetType::class, $projet);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
       
            $projet->setIdUser($user->getIduser());
            
         
            $imageFile = $form->get('imageProjet')->getData();
            
            /*if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                $destination = $this->getParameter('image_directory');
                $imageFile->move($destination, $newFilename);
                $projet->setImageProjet('img/' . $newFilename);  
            }*/

             if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                
             
                $destination = $this->getParameter('image_directory');
                $imageFile->move($destination, $newFilename);
                $projet->setImageProjet('img/' . $newFilename);
      
                $secondaryDestination = 'C:/xampp/htdocs/img';
                if (!file_exists($secondaryDestination)) {
                    mkdir($secondaryDestination, 0777, true);
                }
                copy(
                    $destination . '/' . $newFilename,
                    $secondaryDestination . '/' . $newFilename
                );
            }

          
            foreach ($projet->getEquipes() as $equipe) {
                $currentCount = $equipe->getNbrProjet() ?? 0;
                $equipe->setNbrProjet($currentCount + 1);
                $entityManager->persist($equipe);
            }

            $entityManager->persist($projet);
            $entityManager->flush();

            return $this->redirectToRoute('app_projet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('projet/new.html.twig', [
            'projet' => $projet,
            'form' => $form,
        ]);

    } catch (\Exception $e) {
        $this->addFlash('error', 'Token invalide ou expiré: '.$e->getMessage());
        return $this->redirectToRoute('app_login');
    }
}


    #[Route('/{id}', name: 'app_projet_show', methods: ['GET'])]
    public function show(Projet $projet): Response
    {
        return $this->render('projet/show.html.twig', [
            'projet' => $projet,
        ]);
    }


#[Route('/{id}/edit', name: 'app_projet_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Projet $projet, EntityManagerInterface $entityManager): Response
{
   
    $originalEquipes = new ArrayCollection();
    foreach ($projet->getEquipes() as $equipe) {
        $originalEquipes->add($equipe);
    }
    
    $form = $this->createForm(ProjetType::class, $projet);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('imageProjet')->getData();
        
        /*if ($imageFile) {
            $newFilename = uniqid() . '.' . $imageFile->guessExtension();
            $imageFile->move($this->getParameter('image_directory'), $newFilename);
            $projet->setImageProjet('img/' . $newFilename);
        }*/

         if ($imageFile) {
            $newFilename = uniqid() . '.' . $imageFile->guessExtension();
            
         
            $destination = $this->getParameter('image_directory');
            $imageFile->move($destination, $newFilename);
            $projet->setImageProjet('img/' . $newFilename);
            
           
            $secondaryDestination = 'C:/xampp/htdocs/img';
            if (!file_exists($secondaryDestination)) {
                mkdir($secondaryDestination, 0777, true);
            }
            copy(
                $destination . '/' . $newFilename,
                $secondaryDestination . '/' . $newFilename
            );
        }

     
       foreach ($originalEquipes as $equipe) {
        if (false === $projet->getEquipes()->contains($equipe)) {
           
            $currentCount = $equipe->getNbrProjet() ?? 0;
            $equipe->setNbrProjet(max(0, $currentCount - 1));
            $entityManager->persist($equipe);
        }
    }

    foreach ($projet->getEquipes() as $equipe) {
        if (false === $originalEquipes->contains($equipe)) {
            $currentCount = $equipe->getNbrProjet() ?? 0;
            $equipe->setNbrProjet($currentCount + 1);
            $entityManager->persist($equipe);
        }
    }

        $entityManager->flush();
        return $this->redirectToRoute('app_projet_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('projet/edit.html.twig', [
        'projet' => $projet,
        'form' => $form,
    ]);
}



#[Route('/{id}', name: 'app_projet_delete', methods: ['POST'])]
public function delete(Request $request, Projet $projet, EntityManagerInterface $entityManager): Response
{
    if ($this->isCsrfTokenValid('delete'.$projet->getId(), $request->getPayload()->getString('_token'))) {
   
        foreach ($projet->getEquipes() as $equipe) {
            $currentCount = $equipe->getNbrProjet() ?? 0;
            $newCount = max(0, $currentCount - 1);
            $equipe->setNbrProjet($newCount);
            $entityManager->persist($equipe);
        }

        $entityManager->remove($projet);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_projet_index', [], Response::HTTP_SEE_OTHER);
}

#[Route('/projet/{id}/generate-pdf', name: 'app_projet_pdf', methods: ['POST'])]
public function generatePdf(
    Projet $projet, 
    Request $request,
    PdfGeneratorService $pdfGenerator,
    CloudinaryUploader $cloudinaryUploader
): Response {
    try {
        
        $pdfContent = $pdfGenerator->generateProjetPdf($projet, $projet->getEquipes());
        
   
        $localPath = $this->getParameter('kernel.project_dir').'/public/pdfs/';
        if (!file_exists($localPath)) {
            mkdir($localPath, 0777, true);
        }
        
        $pdfFileName = str_replace(' ', '_', $projet->getNom()).'.pdf';
        $fullLocalPath = $localPath.$pdfFileName;
        file_put_contents($fullLocalPath, $pdfContent);
        

        $cloudinaryUrl = $cloudinaryUploader->uploadPdfToCloudinary($pdfContent);
    
    
        return new Response(
            $pdfContent,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $pdfFileName),
            ]
        );

    } catch (\Exception $e) {
        $this->addFlash('error', 'Erreur: '.$e->getMessage());
        return $this->redirectToRoute('app_projet_index', ['id' => $projet->getId()]);
    }
}


#[Route('/projet/ap/stats', name: 'app_projet_stats_page')]
public function projectStatsPage(ProjetRepository $projetRepository, EquipeRepository $equipeRepository): Response
{
    $totalProjets = $projetRepository->count([]);
    $totalEquipes = $equipeRepository->count([]);

    return $this->render('projet/stats_test.html.twig', [
        'totalProjets' => $totalProjets,
        'totalEquipes' => $totalEquipes,
    ]);
}


#[Route('/projet/api/stats', name: 'app_projet_stats_api')]
public function projectStatsApi(ProjetRepository $projetRepository): JsonResponse
{
    $stats = $projetRepository->createQueryBuilder('p')
        ->select('p.etat as label, COUNT(p.id) as count')
        ->groupBy('p.etat')
        ->getQuery()
        ->getResult();

    $labels = [];
    $data = [];
    $colors = ['#36a2eb', '#4bc0c0', '#ffcd56', '#ff6384'];

    foreach ($stats as $key => $item) {
        $labels[] = $item['label'];
        $data[] = $item['count'];
    }

    return new JsonResponse([
        'labels' => $labels,
        'data' => $data,
        'colors' => $colors,
    ]);
}


#[Route('/projet/api/stats-equipes', name: 'app_projet_stats_equipes_api')]
public function projectStatsByEquipe(ProjetRepository $projetRepository): JsonResponse
{
    $stats = $projetRepository->createQueryBuilder('p')
        ->select('e.nom_equipe as label, COUNT(p.id) as count')
        ->join('p.equipes', 'e')
        ->groupBy('e.nom_equipe')
        ->getQuery()
        ->getResult();

    $labels = [];
    $data = [];
    $colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'];

    foreach ($stats as $item) {
        $labels[] = $item['label'];
        $data[] = $item['count'];
    }

    return new JsonResponse([
        'labels' => $labels,
        'data' => $data,
        'colors' => $colors,
    ]);
}

}