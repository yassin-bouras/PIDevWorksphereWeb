<?php

namespace App\Controller;

use App\Entity\Projet;
use App\Form\ProjetType;
use App\Repository\ProjetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/projet')]
final class ProjetController extends AbstractController{

   /* #[Route(name: 'app_projet_index', methods: ['GET'])]
    public function index(ProjetRepository $projetRepository): Response
    {
        return $this->render('projet/index.html.twig', [
            'projets' => $projetRepository->findAll(),
        ]);
    }*/

    #[Route(name: 'app_projet_index', methods: ['GET'])]
    public function index(ProjetRepository $projetRepository, Request $request): Response
     {
    // Récupérer les paramètres de recherche
    $nom = $request->query->get('search');
    $etat = $request->query->get('etat');
    $nomEquipe = $request->query->get('nomEquipe');

    // Appeler la méthode de recherche
    $projets = $projetRepository->searchProjects($nom, $etat, $nomEquipe);

    // Retourner la vue avec les résultats
    return $this->render('projet/index.html.twig', [
        'projets' => $projets,
    ]);
    }

#[Route('/new', name: 'app_projet_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $projet = new Projet();
    $form = $this->createForm(ProjetType::class, $projet);
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('imageProjet')->getData();
        
        if ($imageFile) {
            $newFilename = uniqid() . '.' . $imageFile->guessExtension();
            $destination = $this->getParameter('image_directory');
            $imageFile->move($destination, $newFilename);
            $projet->setImageProjet('img/' . $newFilename);  
        }

        // Mettre à jour le nombre de projets de l'équipe
        $equipe = $projet->getEquipe();
        if ($equipe) {
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
    $ancienneEquipe = $projet->getEquipe(); 
    
    $form = $this->createForm(ProjetType::class, $projet);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('imageProjet')->getData();
        
        if ($imageFile) {
            $newFilename = uniqid() . '.' . $imageFile->guessExtension();
            $imageFile->move($this->getParameter('image_directory'), $newFilename);
            $projet->setImageProjet('img/' . $newFilename);
        }

        $nouvelleEquipe = $projet->getEquipe();
        
        // Logique de mise à jour du nombre de projets
        if ($ancienneEquipe !== $nouvelleEquipe) {
            // Décrémenter l'ancienne équipe
            if ($ancienneEquipe) {
                $ancienCount = $ancienneEquipe->getNbrProjet() ?? 0;
                $ancienneEquipe->setNbrProjet(max(0, $ancienCount - 1));
                $entityManager->persist($ancienneEquipe);
            }
            
            // Incrémenter la nouvelle équipe
            if ($nouvelleEquipe) {
                $nouveauCount = $nouvelleEquipe->getNbrProjet() ?? 0;
                $nouvelleEquipe->setNbrProjet($nouveauCount + 1);
                $entityManager->persist($nouvelleEquipe);
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
        // Mettre à jour le nombre de projets de l'équipe avant suppression
        $equipe = $projet->getEquipe();
        if ($equipe) {
            $currentCount = $equipe->getNbrProjet() ?? 0;
            $newCount = max(0, $currentCount - 1); // Empêche les valeurs négatives
            $equipe->setNbrProjet($newCount);
            $entityManager->persist($equipe);
        }

        $entityManager->remove($projet);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_projet_index', [], Response::HTTP_SEE_OTHER);
}
}
