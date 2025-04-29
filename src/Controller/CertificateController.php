<?php

namespace App\Controller;

use App\Service\PDFService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CertificateController extends AbstractController
{
    #[Route('/certificate', name: 'app_certificate', methods: ['GET', 'POST'])]
    public function generate(Request $request, PDFService $pdfService): Response
    {
        if ($request->isMethod('POST')) {
            $firstName = $request->request->get('first_name');
            $lastName = $request->request->get('last_name');
            $courseTitle = $request->request->get('course_title');

            if ($firstName && $lastName && $courseTitle) {
                $pdfContent = $pdfService->generateCertificate($firstName, $lastName, $courseTitle);

                return new Response(
                    $pdfContent,
                    200,
                    [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'attachment; filename="certificat_'.$lastName.'.pdf"'
                    ]
                );
            } else {
                $this->addFlash('error', 'Tous les champs sont obligatoires.');
            }
        }

        return $this->render('certificate/index.html.twig');
    }
}
