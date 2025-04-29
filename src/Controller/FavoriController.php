<?php

namespace App\Controller;

use App\Entity\Favori;
use App\Form\FavoriType;
use App\Repository\FavoriRepository;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use App\Repository\UserRepository;


#[Route('/favori')]
class FavoriController extends AbstractController
{

    private $jwtEncoder;
    private $userRepository;

    public function __construct(JWTEncoderInterface $jwtEncoder, UserRepository $userRepository)
    {
        $this->jwtEncoder = $jwtEncoder;
        $this->userRepository = $userRepository;
    }


    #[Route('/add/{formation_id}', name: 'app_favori_add')]
    public function addToFavori(
        int $formation_id,
        
        EntityManagerInterface $em,
        FavoriRepository $favoriRepository
    ): RedirectResponse {
        
        $existing = $favoriRepository->findOneBy(['id_f' => $formation_id]);

        if (!$existing) {
            $favori = new Favori();
            $favori->setIdF($formation_id);
            $favori->setIduser(39);
            $em->persist($favori);
            $em->flush();

            $this->addFlash('success', 'Formation ajoutée aux favoris');
        } else {
            $this->addFlash('warning', 'Formation déjà présente dans les favoris');
        }

        return $this->redirectToRoute('app_formation_index2');
    }

    #[Route('/remove/{id}', name: 'app_favori_remove')]
    public function removeFromFavori(int $id, FavoriRepository $favoriRepository, EntityManagerInterface $em): RedirectResponse
    {
        $favori = $favoriRepository->find($id);
        if ($favori) {
            $em->remove($favori);
            $em->flush();
            $this->addFlash('info', 'Formation retirée des favoris');
        }

        return $this->redirectToRoute('app_favori_list');
    }

    #[Route('/list', name: 'app_favori_list')]
    public function listFavoris(FavoriRepository $favoriRepository, FormationRepository $formationRepository): Response
    {
        $favoris = $favoriRepository->findAll();

        $formationsFavoris = [];
        foreach ($favoris as $favori) {
            $formation = $formationRepository->find($favori->getIdF());
            if ($formation) {
                $formationsFavoris[] = $formation;
            }
        }

        return $this->render('favori/index.html.twig', [
            'formations' => $formationsFavoris,
            'favoris' => $favoris,
        ]);
    }
}
