<?php

namespace App\Controller;

use App\Entity\Candidature;
use App\Entity\Entretien;
use App\Entity\Offre;
use App\Entity\User;
use App\Form\EntretienType;
use App\Repository\CandidatureRepository;
use App\Repository\EntretienRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/entretien')]
final class EntretienController extends AbstractController{
    #[Route(name: 'app_entretien_index', methods: ['GET'])]
    public function index(EntretienRepository $entretienRepository): Response
    {
        return $this->render('entretien/index.html.twig', [
            'entretiens' => $entretienRepository->findAll(),
        ]);
    }

    #[Route('/employee', name: 'entretien_by_employee')]
    public function showEntretienByEmployee( EntretienRepository $entretienRepository): Response
    {
        $entretiens = $entretienRepository->findByEmployeeId(50);

        return $this->render('entretien/index2.html.twig', [
            'entretiens' => $entretiens,
        ]);
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




    #[Route('/{id}', name: 'app_entretien_show', methods: ['GET'])]
public function show(Entretien $entretien, EntityManagerInterface $em): Response
{
    $candidat = null;

    $candidatId = $entretien->getCandidatId();

    if ($candidatId !== null) {
        $candidat = $em->getRepository(User::class)->find($candidatId);
    }

    return $this->render('entretien/show.html.twig', [
        'entretien' => $entretien,
        'candidat' => $candidat,
    ]); }

   

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
        if ($this->isCsrfTokenValid('delete'.$entretien->getId(), $request->getPayload()->getString('_token'))) {
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

    



}

   











