<?php

namespace App\Controller;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use App\Entity\User; // Make sure you import the User entity

use App\Entity\Offre;
use App\Form\OffreType;
use App\Repository\OffreRepository;
use App\Repository\CandidatureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/offre')]
final class OffreController extends AbstractController
{
    #[Route(name: 'app_offre_index', methods: ['GET'])]
    public function index(Request $request, OffreRepository $offreRepository): Response
    {
        $search = $request->query->get('search', '');
        $offres = $offreRepository->findByTitre($search);


        return $this->render('offre/index.html.twig', [
            'offres' => $offres,
        ]);
    }

    #[Route('/front', name: 'app_offre_front_index', methods: ['GET'])]
    public function frontIndex(Request $request, OffreRepository $offreRepository): Response
    {
        $search = $request->query->get('search', '');
        $offres = $offreRepository->findByTitre($search);

        return $this->render('offre/front_index.html.twig', [
            'offres' => $offres,
            'search' => $search,
            'context' => 'front'
        ]);
    }

    #[Route('/new', name: 'app_offre_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $offre = new Offre();
        $offre->setDatePublication(new \DateTime());

        $form = $this->createForm(OffreType::class, $offre);
        $form->handleRequest($request);

        // Check form is submitted AND valid BEFORE trying to get user
        if ($form->isSubmitted() && $form->isValid()) {
            $userId = $request->request->get('iduser');

            if (!$userId) {
                throw new \Exception("Le champ 'user_id' est manquant dans le formulaire.");
            }

            $user = $entityManager->getRepository(User::class)->find($userId);
            if (!$user) {
                throw $this->createNotFoundException("Utilisateur avec l'ID $userId introuvable.");
            }

            $offre->addUser($user); // or setUser()

            if (!$offre->getDateLimite()) {
                $offre->setDateLimite(new \DateTime());
            }

            $entityManager->persist($offre);
            $entityManager->flush();

            return $this->redirectToRoute('app_offre_index');
        }

        return $this->render('offre/new.html.twig', [
            'offre' => $offre,
            'form' => $form,
            'context' => $request->get('context', 'back'),
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


    #[Route('/{id_offre}/candidatures', name: 'app_offre_candidatures', methods: ['GET'])]
    public function candidatures(Offre $offre, CandidatureRepository $candidatureRepository): Response
    {
        $candidatures = $candidatureRepository->findBy(['offre' => $offre]);

        return $this->render('offre/candidatures.html.twig', [
            'offre' => $offre,
            'candidatures' => $candidatures,
        ]);
    }
}
