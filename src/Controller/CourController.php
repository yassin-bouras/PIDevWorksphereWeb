<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Form\CoursType;
use App\Repository\CoursRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cour')]
final class CourController extends AbstractController
{
    #[Route(name: 'app_cour_index', methods: ['GET'])]
    public function index(CoursRepository $coursRepository): Response
    {
        return $this->render('cour/index.html.twig', [
            'cours' => $coursRepository->findAll(),
        ]);
    }

   #[Route('/new', name: 'app_cour_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $cour = new Cours();
    $form = $this->createForm(CoursType::class, $cour);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $photoFile = $form->get('photo')->getData();

        if ($photoFile) {
            // Supprimer l'ancienne image si elle existe
            $oldPhoto = $cour->getPhoto();
            if ($oldPhoto) {
                $oldFilename = basename($oldPhoto);

                // Supprimer dans /public
                $oldPathPublic = $this->getParameter('kernel.project_dir') . '/public/' . $oldPhoto;
                if (file_exists($oldPathPublic)) {
                    unlink($oldPathPublic);
                }

                // Supprimer dans le dossier secondaire
                $oldPathSecondary = 'C:/xampp/htdocs/img/' . $oldFilename;
                if (file_exists($oldPathSecondary)) {
                    unlink($oldPathSecondary);
                }
            }

            // Générer le nouveau nom de fichier propre et unique
            $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = time() . '_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalFilename) . '.' . $photoFile->guessExtension();

            // Destination principale (public)
            $destination = $this->getParameter('kernel.project_dir') . '/public/img';
            $photoFile->move($destination, $newFilename);

            $cour->setPhoto('img/' . $newFilename);

            // Copie vers le dossier secondaire
            $secondaryDestination = 'C:/xampp/htdocs/img';
            copy(
                $destination . '/' . $newFilename,
                $secondaryDestination . '/' . $newFilename
            );
        }

        $entityManager->persist($cour);
        $entityManager->flush();

        return $this->redirectToRoute('app_cour_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('cour/new.html.twig', [
        'form' => $form->createView(),
        'cour' => $cour,
    ]);
}

    #[Route('/{id_c}', name: 'app_cour_show', methods: ['GET'])]
    public function show(Cours $cour): Response
    {
        return $this->render('cour/show.html.twig', [
            'cour' => $cour,
        ]);
    }

    

    #[Route('/search', name: 'app_cour_search', methods: ['GET'])]
    public function search(Request $request, CoursRepository $coursRepository): Response
    {
        $searchTerm = $request->query->get('search');
    
        $cours = $coursRepository->createQueryBuilder('c')
            ->where('c.nomC LIKE :search')
            ->setParameter('search', '%' . $searchTerm . '%')
            ->getQuery()
            ->getResult();
    
        return $this->render('cour/index.html.twig', [
            'cours' => $cours,
        ]);
    }
    
    #[Route('/cour/filter', name: 'app_cour_filter', methods: ['GET'])]
    public function filter(Request $request, CoursRepository $coursRepository): Response
    {
        $search = $request->query->get('search');
        $date = $request->query->get('date');
        $heureDebut = $request->query->get('heureDebut');
        $heureFin = $request->query->get('heureFin');
    
        $queryBuilder = $coursRepository->createQueryBuilder('c');
    
        if ($search) {
            $queryBuilder
                ->andWhere('c.nomC LIKE :search OR c.descriptionC LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }
    
        if ($date) {
            $queryBuilder
                ->andWhere('c.date = :date')
                ->setParameter('date', new \DateTime($date));
        }
    
        if ($heureDebut) {
            $queryBuilder
                ->andWhere('c.heureDebut = :heureDebut')
                ->setParameter('heureDebut', new \DateTime($heureDebut));
        }
    
        if ($heureFin) {
            $queryBuilder
                ->andWhere('c.heureFin = :heureFin')
                ->setParameter('heureFin', new \DateTime($heureFin));
        }
    
        $cours = $queryBuilder->getQuery()->getResult();
    
        return $this->render('cour/index.html.twig', [
            'cours' => $cours,
        ]);
    }
    
   #[Route('/{id_c}/edit', name: 'app_cour_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Cours $cour, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(CoursType::class, $cour);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $photoFile = $form->get('photo')->getData();

        if ($photoFile) {
            $oldPhoto = $cour->getPhoto();
            if ($oldPhoto) {
                $oldFilename = basename($oldPhoto);

                // Supprimer l'image dans /public
                $oldPathPublic = $this->getParameter('kernel.project_dir') . '/public/' . $oldPhoto;
                if (file_exists($oldPathPublic)) {
                    unlink($oldPathPublic);
                }

                // Supprimer l'image dans C:/xampp/htdocs/img
                $oldPathSecondary = 'C:/xampp/htdocs/img/' . $oldFilename;
                if (file_exists($oldPathSecondary)) {
                    unlink($oldPathSecondary);
                }
            }

            // Génération du nouveau nom
            $newFilename = uniqid() . '.' . $photoFile->guessExtension();

            // Chemin principal
            $destination = $this->getParameter('kernel.project_dir') . '/public/img';
            $photoFile->move($destination, $newFilename);
            $cour->setPhoto('img/' . $newFilename);

            // Copie vers le dossier secondaire
            $secondaryDestination = 'C:/xampp/htdocs/img';
            copy(
                $destination . '/' . $newFilename,
                $secondaryDestination . '/' . $newFilename
            );
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_cour_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('cour/edit.html.twig', [
        'cour' => $cour,
        'form' => $form,
    ]);
}

    #[Route('/{id_c}', name: 'app_cour_delete', methods: ['POST'])]
    public function delete(Request $request, Cours $cour, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cour->getIdC(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($cour);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cour_index', [], Response::HTTP_SEE_OTHER);
    }
}
