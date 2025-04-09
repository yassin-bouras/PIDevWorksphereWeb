<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/formation')]
final class FormationController extends AbstractController{
    #[Route(name: 'app_formation_index', methods: ['GET'])]
    public function index(FormationRepository $formationRepository): Response
    {
        return $this->render('formation/index.html.twig', [
            'formations' => $formationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_formation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $formation = new Formation();
        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
         
            $photoFile = $form->get('photo')->getData();
            if ($photoFile) {
                // Générer un nom unique pour le fichier
                $newFilename = uniqid() . '.' . $photoFile->guessExtension();
                
                $photoFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/img',
                    $newFilename
                );
             
                $formation->setPhoto('img/' . $newFilename);
            }
            $entityManager->persist($formation);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('formation/new.html.twig', [
            'formation' => $formation,
            'form' => $form,
        ]);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($formation);
            $entityManager->flush();
        
            return $this->redirectToRoute('app_formation_index');
        } else {
            // Ajoute cette ligne pour voir les erreurs dans le dump
            dump($form->getErrors(true));
        }
    }
    

    #[Route('/{id_f}', name: 'app_formation_show', methods: ['GET'])]
    public function show(Formation $formation): Response
    {
        return $this->render('formation/show.html.twig', [
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
             
                if ($formation->getPhoto()) {
                    unlink($this->getParameter('kernel.project_dir') . '/public/' . $formation->getPhoto());
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
<<<<<<< Updated upstream
        if ($this->isCsrfTokenValid('delete'.$formation->getIdF(), $request->getPayload()->getString('_token'))) {
=======
        if ($this->isCsrfTokenValid('delete'.$formation->getIdf(), $request->getPayload()->getString('_token'))) {
>>>>>>> Stashed changes
            $entityManager->remove($formation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_formation_index', [], Response::HTTP_SEE_OTHER);
    }
}
