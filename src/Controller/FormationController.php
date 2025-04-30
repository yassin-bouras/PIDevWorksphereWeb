<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Entity\Cours;
use App\Entity\FormationCours;
use App\Form\FormationType;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use App\Repository\UserRepository;


#[Route('/formation')]
final class FormationController extends AbstractController
{
    
    private $jwtEncoder;
    private $userRepository;

    public function __construct(JWTEncoderInterface $jwtEncoder, UserRepository $userRepository)
    {
        $this->jwtEncoder = $jwtEncoder;
        $this->userRepository = $userRepository;
    }


    #[Route(name: 'app_formation_index', methods: ['GET'])]
    public function index(FormationRepository $formationRepository): Response
    {
        return $this->render('formation/index.html.twig', [
            'formations' => $formationRepository->findAll(),
        ]);
    }

    #[Route('/employe', name:'app_formation_index2', methods: ['GET'])]
    public function index2(FormationRepository $formationRepository): Response
    {
        return $this->render('formation/index2.html.twig', [
            'formations' => $formationRepository->findAll(),
        ]);
    }
    
    
    #[Route('/search', name: 'app_formation_search1', methods: ['GET'])]
    public function search1(Request $request, FormationRepository $formationRepository): Response
{
    $searchTerm = $request->query->get('search');

    $formations = $formationRepository->createQueryBuilder('f')
        ->where('f.titre LIKE :search')
        ->setParameter('search', '%' . $searchTerm . '%')
        ->getQuery()
        ->getResult();

    return $this->render('formation/index.html.twig', [
        'formations' => $formations,
    ]);
}

#[Route('/formation/search', name: 'app_formation_filter1', methods: ['GET'])]
public function filter(Request $request, FormationRepository $formationRepository): Response
{
    $search = $request->query->get('search');
    $certifie = $request->query->get('certifie');
    $type = $request->query->get('type');
    $langue = $request->query->get('langue');

    $queryBuilder = $formationRepository->createQueryBuilder('f');

    if ($search) {
        $queryBuilder
            ->andWhere('f.titre LIKE :search OR f.description LIKE :search')
            ->setParameter('search', '%' . $search . '%');
    }

    if ($certifie !== null && $certifie !== '') {
        $queryBuilder
            ->andWhere('f.certifie = :certifie')
            ->setParameter('certifie', $certifie);
    }

    if ($type) {
        $queryBuilder
            ->andWhere('f.type = :type')
            ->setParameter('type', $type);
    }

    if ($langue) {
        $queryBuilder
            ->andWhere('f.langue = :langue')
            ->setParameter('langue', $langue);
    }

    $formations = $queryBuilder->getQuery()->getResult();

    return $this->render('formation/index.html.twig', [
        'formations' => $formations,
    ]);
}


    #[Route('/employe/search', name: 'app_formation_search', methods: ['GET'])]
    public function search(Request $request, FormationRepository $formationRepository): Response
{
    $searchTerm = $request->query->get('search');

    $formations = $formationRepository->createQueryBuilder('f')
        ->where('f.titre LIKE :search')
        ->setParameter('search', '%' . $searchTerm . '%')
        ->getQuery()
        ->getResult();

    return $this->render('formation/index2.html.twig', [
        'formations' => $formations,
    ]);
}
#[Route('/employe/filter', name: 'app_formation_filter2', methods: ['GET'])]
public function filter2(Request $request, FormationRepository $formationRepository): Response
{
    $search = $request->query->get('search');
    $certifie = $request->query->get('certifie');
    $type = $request->query->get('type');
    $langue = $request->query->get('langue');

    $queryBuilder = $formationRepository->createQueryBuilder('f');

    if ($search) {
        $queryBuilder
            ->andWhere('f.titre LIKE :search OR f.description LIKE :search')
            ->setParameter('search', '%' . $search . '%');
    }

    if ($certifie !== null && $certifie !== '') {
        $queryBuilder
            ->andWhere('f.certifie = :certifie')
            ->setParameter('certifie', $certifie);
    }

    if ($type) {
        $queryBuilder
            ->andWhere('f.type = :type')
            ->setParameter('type', $type);
    }

    if ($langue) {
        $queryBuilder
            ->andWhere('f.langue = :langue')
            ->setParameter('langue', $langue);
    }

    $formations = $queryBuilder->getQuery()->getResult();

    return $this->render('formation/index2.html.twig', [
        'formations' => $formations,
    ]);
}

 #[Route('/new', name: 'app_formation_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    // Récupérer le token dans les cookies
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

        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $newFilename = uniqid() . '.' . $photoFile->guessExtension();
                $photoFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/img',
                    $newFilename
                );
                $formation->setPhoto('img/' . $newFilename);
            }

         
            $formation->setIdUser($user->getIduser());

            $entityManager->persist($formation);
            $entityManager->flush();

            return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('formation/new.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);

    } catch (\Exception $e) {
        $this->addFlash('error', 'Token invalide ou expiré.');
        return $this->redirectToRoute('app_login');
    }
}


    #[Route('/{id_f}', name: 'app_formation_show', methods: ['GET'])]
    public function show(Formation $formation): Response
    {
        return $this->render('formation/show.html.twig', [
            'formation' => $formation,
        ]);
    }
    #[Route('/employe/{id_f}', name: 'app_formation_show2', methods: ['GET'])]
    public function show2(Formation $formation): Response
    {
        return $this->render('formation/show2.html.twig', [
            'formation' => $formation,
        ]);
    }
    #[Route('/{id_f}/edit', name: 'app_formation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                $oldPhoto = $formation->getPhoto();
                if ($oldPhoto) {
                    $oldPath = $this->getParameter('kernel.project_dir') . '/public/' . $oldPhoto;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $newFilename = uniqid() . '.' . $photoFile->guessExtension();
                $photoFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/img',
                    $newFilename
                );
                $formation->setPhoto('img/' . $newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('formation/edit.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
    }

    #[Route('/{id_f}', name: 'app_formation_delete', methods: ['POST'])]
    public function delete(Request $request, Formation $formation, EntityManagerInterface $entityManager): Response
    {
        $token = $request->request->get('_token');

        if ($this->isCsrfTokenValid('delete' . $formation->getIdF(), $token)) {
            $entityManager->remove($formation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/formation/{id_f}/assign', name: 'app_formation_assign_cours')]
    public function assignCours(Request $request, ManagerRegistry $doctrine, int $id_f): Response
    {
        $entityManager = $doctrine->getManager();
        $formation = $entityManager->getRepository(Formation::class)->find($id_f);
        $coursList = $entityManager->getRepository(Cours::class)->findAll();
    
        if ($request->isMethod('POST')) {
            $coursId = $request->request->get('cours_id');
            $coursChoisi = $entityManager->getRepository(Cours::class)->find($coursId);
    
            $formationCours = new FormationCours();
            $formationCours->setFormation($formation);
            $formationCours->setCours($coursChoisi);
    
            $entityManager->persist($formationCours);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_formation_index');
        }
    
        return $this->render('formation/assign.html.twig', [
            'formation' => $formation,
            'cours' => $coursList,
        ]);
    }

#[Route('/{id_f}/cours', name: 'app_formation_cours')]
public function showCours(int $id_f, FormationRepository $formationRepository): Response
{
    $formation = $formationRepository->find($id_f);

    if (!$formation) {
        throw $this->createNotFoundException('Formation non trouvée');
    }

    $cours = $formation->getCours(); 

    return $this->render('formation/cours.html.twig', [
        'formation' => $formation,
        'cours' => $cours,
    ]);
}
#[Route('/employe/{id_f}/cours', name: 'app2_formation_cours')]
public function showCours2(int $id_f, FormationRepository $formationRepository): Response
{
    $formation = $formationRepository->find($id_f);

    if (!$formation) {
        throw $this->createNotFoundException('Formation non trouvée');
    }

    $cours = $formation->getCours(); 

    return $this->render('formation/cours2.html.twig', [
        'formation' => $formation,
        'cours' => $cours,
    ]);
}



}
