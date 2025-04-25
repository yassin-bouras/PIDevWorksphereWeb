<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PdfGeneratorService
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    
    public function generateProjetPdf($projet, $equipes = null): Response
    {
        $pdfOptions = new Options();
    $pdfOptions->set('defaultFont', 'Arial');
    $pdfOptions->set('isRemoteEnabled', true);
    
    $dompdf = new Dompdf($pdfOptions);
    
    $html = $this->twig->render('projet/projetPDF.html.twig', [
        'projet' => $projet,
        'equipes' => $equipes, 
        'date' => new \DateTime()
    ]);
        
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $output = $dompdf->output();
        
        $response = new Response($output);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'attachment; filename="projet_'.$projet->getId().'.pdf"');
        
        return $response;
    }
}