<?php

namespace App\Controller;

use App\Entity\EntretienHistory;
use App\Repository\EntretienHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EntretienHistoryController extends AbstractController{

    private $entityManager;


    public function __construct(EntityManagerInterface  $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/history', name: 'entretien_history_list')]
    

        public function showHistory(EntretienHistoryRepository $repository): Response
    {
        $history = $repository->findAll();

        return $this->render('entretien_history/index.html.twig', [
            'history' => $history,
        ]);
    }
       

   
}
