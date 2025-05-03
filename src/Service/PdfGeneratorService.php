<?php

namespace App\Service;

use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class PdfGeneratorService
{
    private Environment $twig;
    private Pdf $pdf;

    public function __construct(Environment $twig, Pdf $pdf)
    {
        $this->twig = $twig;
        $this->pdf = $pdf;
    }

    /**
     * Generates a PDF string from a projet and optional equipes.
     *
     * @param mixed $projet The projet data to render.
     * @param mixed|null $equipes Optional list of equipes.
     * @return string The generated PDF binary content.
     */
    public function generateProjetPdf($projet, $equipes = null): string
    {
        $html = $this->twig->render('projet/projetPDF.html.twig', [
            'projet' => $projet,
            'equipes' => $equipes,
            'date' => new \DateTime(),
        ]);

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
