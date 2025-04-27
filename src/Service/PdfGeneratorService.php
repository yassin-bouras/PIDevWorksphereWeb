<?php

namespace App\Service;

use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PdfGeneratorService
{
    private $twig;
    private $pdf;

    public function __construct(Environment $twig, Pdf $pdf)
    {
        $this->twig = $twig;
        $this->pdf = $pdf;
    }

    public function generateProjetPdf($projet, $equipes = null): string
    {
        $html = $this->twig->render('projet/projetPDF.html.twig', [
            'projet' => $projet,
            'equipes' => $equipes, 
            'date' => new \DateTime()
        ]);
        
        // Génère le PDF et retourne le contenu binaire
        return $this->pdf->getOutputFromHtml($html, [
            'encoding' => 'utf-8',
            'enable-javascript' => true,
            'javascript-delay' => 1000,
            'no-stop-slow-scripts' => true,
            'no-background' => false,
            'lowquality' => false,
            'page-size' => 'A4',
            'margin-top' => 10,
            'margin-right' => 10,
            'margin-bottom' => 10,
            'margin-left' => 10,
            'dpi' => 300,
            'image-dpi' => 300,
            'enable-external-links' => true,
            'enable-internal-links' => true,
            
        ]);
    }
}