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


#[Route('/equipe')]
final class EquipeController extends AbstractController{
    
   
    #[Route(name: 'app_equipe_index', methods: ['GET'])]
    public function index(EquipeRepository $equipeRepository, Request $request): Response
    {
        $searchTerm = $request->query->get('search');

        if ($searchTerm) {
            $equipes = $equipeRepository->findByNomEquipe($searchTerm);
        } else {
            $equipes = $equipeRepository->findAll();
        }

        return $this->render('equipe/index.html.twig', [
            'equipes' => $equipes,
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
                
                // Répertoire de stockage des images dans le répertoire public
                $destination = $this->getParameter('image_directory');  // public/img
                
                // Déplacer l'image dans le répertoire public
                $imageFile->move($destination, $newFilename);
                
                // Enregistrer le chemin relatif de l'image (cela sera utilisé dans le front-end de Symfony)
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
        // Check if a new image has been uploaded
        $imageFile = $form->get('imageEquipe')->getData(); // Assuming the field name is 'imageProjet'

        if ($imageFile) {
            // Generate a unique filename
            $newFilename = uniqid() . '.' . $imageFile->guessExtension();

            // Move the file to the appropriate directory
            $imageFile->move(
                $this->getParameter('image_directory'),
                $newFilename
            );

            // Update the image in the project entity (save only the relative path)
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

   
}
