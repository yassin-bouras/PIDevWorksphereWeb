<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard/base2.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }

    #[Route('/dashboardcandidat', name: 'app_dashboard_candidat')]
    public function candidat(): Response
    {
        return $this->render('dashboard/base2.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }

    #[Route('/dashboardmanager', name: 'app_dashboard_manager')]
    public function manager(): Response
    {
        return $this->render('dashboard/manager.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }

    #[Route('/dashboardemploye', name: 'app_dashboard_employe')]
    public function employe(): Response
    {
        return $this->render('dashboard/employe.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }
}
