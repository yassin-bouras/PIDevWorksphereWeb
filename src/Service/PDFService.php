<?php

namespace App\Service;

use Mpdf\Mpdf;

class PDFService
{
    public function generateCertificate(string $firstName, string $lastName, string $courseTitle): string
    {
        $mpdf = new Mpdf();

       
        $logoPath = __DIR__.'/../../public/front/images/logo-icon.png';
        $logoData = base64_encode(file_get_contents($logoPath));
        $logoSrc = 'data:image/png;base64,'.$logoData;

        $html = "
        <style>
            .certificate {
                border: 15px solid #1a5276;
                padding: 40px;
                text-align: center;
                font-family: 'Helvetica', Arial, sans-serif;
                background-color: #f9f9f9;
                position: relative;
            }
            .logo {
                max-width: 150px;
                margin: 0 auto 20px;
            }
            h1 {
                color: #1a5276;
                font-size: 28px;
                margin-bottom: 30px;
                text-transform: uppercase;
            }
            h2 {
                color: #2874a6;
                font-size: 24px;
                margin: 20px 0;
            }
            h3 {
                color: #3498db;
                font-size: 20px;
                font-style: italic;
            }
            .signature {
                margin-top: 50px;
                display: flex;
                justify-content: space-between;
                width: 70%;
                margin-left: auto;
                margin-right: auto;
            }
            .signature-line {
                border-top: 1px solid #000;
                width: 150px;
                display: inline-block;
                margin-top: 50px;
            }
            .date {
                margin-top: 30px;
                font-weight: bold;
            }
            .decoration {
                position: absolute;
                width: 100px;
                height: 100px;
                opacity: 0.1;
            }
            .decoration-1 {
                top: 20px;
                left: 20px;
                border-top: 5px solid #1a5276;
                border-left: 5px solid #1a5276;
            }
            .decoration-2 {
                bottom: 20px;
                right: 20px;
                border-bottom: 5px solid #1a5276;
                border-right: 5px solid #1a5276;
            }
        </style>

        <div class='certificate'>
            <div class='decoration decoration-1'></div>
            <div class='decoration decoration-2'></div>
            
            <img src='{$logoSrc}' class='logo' alt='Logo de la formation'>
            
            
            <h1>Certificat de Réussite</h1>
            
            <p>Ce certificat est décerné à</p>
            <h2>{$firstName} {$lastName}</h2>
            
            <p>pour avoir suivi et validé avec succès la formation</p>
            <h3>« {$courseTitle} »</h3>
            
            <p>Cette attestation reconnaît l'engagement et les compétences acquises.</p>
            
            <div class='signature'>
                <div>
                    <div class='signature-line'></div>
                    <p>Directeur de la formation</p>
                </div>
                <div>
                    <div class='signature-line'></div>
                    <p>Date</p>
                </div>
            </div>
            
            <div class='date'>Délivré le ".date('d/m/Y')."</div>
        </div>
        ";

        $mpdf->WriteHTML($html);

        return $mpdf->Output('', 'S');
    }
}