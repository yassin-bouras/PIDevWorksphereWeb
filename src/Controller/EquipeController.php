<?php

namespace App\Controller;

use App\Entity\Equipe;
use App\Entity\Projet;
use App\Form\EquipeType;
use App\Repository\EquipeRepository;
use App\Repository\ProjetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\PdfGeneratorService;

#[Route('/equipe')]
final class EquipeController extends AbstractController{
    
   
#[Route(name: 'app_equipe_index', methods: ['GET'])]
public function index(EquipeRepository $equipeRepository, ProjetRepository $projetRepository, Request $request): Response
{
    $searchTerm = $request->query->get('search');
    $equipes = $searchTerm ? $equipeRepository->findByNomEquipe($searchTerm) : $equipeRepository->findAll();
    
    return $this->render('equipe/index.html.twig', [
        'equipes' => $equipes,
        'all_projets' => $projetRepository->findAll(),
    ]);
}


    #[Route('/equipefront',name: 'AfficherEquipeFront', methods: ['GET'])]
    public function AfficherEquipe(EquipeRepository $equipeRepository, ProjetRepository $projetRepository, Request $request): Response
    {
        $searchTerm = $request->query->get('search');
        if ($searchTerm) {
            $equipes = $equipeRepository->searchTeamsAndProjects($searchTerm);
        } else {
            $equipes = $equipeRepository->findAllWithProjects();
        }
    
        return $this->render('equipe/AfficherEquipe.html.twig', [
            'equipes' => $equipes,
            'searchTerm' => $searchTerm
        ]);
    }

  
    
    #[Route('/equipefront/{id}', name: 'AfficherDetailsEq', methods: ['GET'])]
    public function AfficherDetailsEquipe(Equipe $equipe): Response
    {
        return $this->render('equipe/AfficherDetailsEquipe.html.twig', [
            'equipe' => $equipe,
        ]);
    }

    /* avec la search ajax #[Route(name: 'app_equipe_index', methods: ['GET'])]
    public function index(EquipeRepository $equipeRepository, Request $request): Response
    {
        $searchTerm = $request->query->get('search');
        
        if ($request->isXmlHttpRequest()) {
            $equipes = $equipeRepository->findByNomEquipe($searchTerm);
            $data = [];
            
            foreach ($equipes as $equipe) {
                $imagePath = $equipe->getImageEquipe() ? 
                    $this->getParameter('kernel.project_dir').'/public'.$equipe->getImageEquipe() : 
                    null;
                
                $data[] = [
                    'id' => $equipe->getId(),
                    'nomEquipe' => $equipe->getNomEquipe(),
                    'nbrProjet' => $equipe->getNbrProjet(),
                    'imageEquipe' => $equipe->getImageEquipe(), // Stockez seulement le chemin relatif
                    'showUrl' => $this->generateUrl('app_equipe_show', ['id' => $equipe->getId()]),
                    'editUrl' => $this->generateUrl('app_equipe_edit', ['id' => $equipe->getId()]),
                    'deleteUrl' => $this->generateUrl('app_equipe_delete', ['id' => $equipe->getId()]),
                ];
            }
            
            return $this->json($data);
        }
        
        $equipes = $searchTerm ? $equipeRepository->findByNomEquipe($searchTerm) : $equipeRepository->findAll();
        
        return $this->render('equipe/index.html.twig', [
            'equipes' => $equipes,
        ]);
    }*/

   




    #[Route('/new', name: 'app_equipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $equipe = new Equipe();
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            

            $imageFile = $form->get('imageEquipe')->getData();
        
            if ($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();
                
                $destination = $this->getParameter('image_directory');  
                
                $imageFile->move($destination, $newFilename);
                
                $equipe->setImageEquipe('img/' . $newFilename);  
            }

    

            
            $entityManager->persist($equipe);
            $entityManager->flush();

            return $this->redirectToRoute('app_equipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('equipe/new.html.twig', [
            'equipe' => $equipe,
            'form' => $form,
        ]);
    }
 


    #[Route('/{id}', name: 'app_equipe_show', methods: ['GET'])]
    public function show(Equipe $equipe): Response
    {
        return $this->render('equipe/show.html.twig', [
            'equipe' => $equipe,
        ]);
    }

   

    #[Route('/{id}/edit', name: 'app_equipe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);
      
    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('imageEquipe')->getData(); 

        if ($imageFile) {
            $newFilename = uniqid() . '.' . $imageFile->guessExtension();

            $imageFile->move(
                $this->getParameter('image_directory'),
                $newFilename
            );

            $equipe->setImageEquipe('img/' . $newFilename);
        }
            $entityManager->flush();

            return $this->redirectToRoute('app_equipe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('equipe/edit.html.twig', [
            'equipe' => $equipe,
            'form' => $form,
        ]);
    }

   

    #[Route('/{id}', name: 'app_equipe_delete', methods: ['POST'])]
    public function delete(Request $request, Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$equipe->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($equipe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_equipe_index', [], Response::HTTP_SEE_OTHER);
    }


 #[Route('/{id}/assign-project', name: 'app_equipe_assign_project', methods: ['POST'])]
public function assignProject(Request $request, Equipe $equipe, EntityManagerInterface $entityManager, ProjetRepository $projetRepository): Response
{
    $projetId = $request->request->get('projet_id');
    $projet = $projetRepository->find($projetId);

    if (!$projet) {
        $this->addFlash('error', 'Projet non trouvé.');
        return $this->redirectToRoute('app_equipe_index');
    }

    $projet->setEquipe($equipe);
    
    $equipe->setNbrProjet($equipe->getProjets()->count());

    $entityManager->flush();

    $this->addFlash('success', 'Projet assigné avec succès à l\'équipe.');
    return $this->redirectToRoute('app_equipe_index');
}


/*#[Route('/{id}/assign-project', name: 'app_equipe_assign_project', methods: ['POST'])]
public function assignProject(Request $request, Equipe $equipe, EntityManagerInterface $entityManager, ProjetRepository $projetRepository): Response
{
    $projetId = $request->request->get('projet_id');
    $projet = $projetRepository->find($projetId);

    if (!$projet) {
        $this->addFlash('error', 'Projet non trouvé.');
        return $this->redirectToRoute('app_equipe_index');
    }

    // Ajoute le projet à l'équipe (relation ManyToMany)
    if (!$equipe->getProjets()->contains($projet)) {
        $equipe->addProjet($projet);
        $entityManager->flush();
        $this->addFlash('success', 'Projet assigné avec succès à l\'équipe.');
    } else {
        $this->addFlash('warning', 'Ce projet est déjà assigné à cette équipe.');
    }

    return $this->redirectToRoute('app_equipe_index');
}
   */

}