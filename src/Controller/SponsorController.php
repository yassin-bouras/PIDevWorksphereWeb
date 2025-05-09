<?php

namespace App\Controller;

use App\Entity\Sponsor;
use App\Form\SponsorType;
use App\Repository\EvenementSponsorRepository;
use App\Service\GeminiAIService;
use App\Repository\SponsorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use TCPDF;
use App\Repository\EvennementRepository;

#[Route('/sponsor')]
final class SponsorController extends AbstractController{
    #[Route(name: 'app_sponsor_index', methods: ['GET'])]
    public function index(SponsorRepository $sponsorRepository, Request $request): Response
    {
        $searchTerm = $request->query->get('search');
        $budgetFilter = $request->query->get('budget');

        $sponsors = $sponsorRepository->findBySearchAndBudget($searchTerm, $budgetFilter);

        return $this->render('sponsor/index.html.twig', [
            'sponsors' => $sponsors,
        ]);
    }

    // Dans SponsorController.php

#[Route('/new', name: 'app_sponsor_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $sponsor = new Sponsor();
    $form = $this->createForm(SponsorType::class, $sponsor);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Les méthodes getBudgetApresReduction() et getClassement() seront appelées automatiquement
        $entityManager->persist($sponsor);
        $entityManager->flush();

        return $this->redirectToRoute('app_sponsor_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('sponsor/new.html.twig', [
        'sponsor' => $sponsor,
        'form' => $form,
    ]);
}

    #[Route('/{idSponsor}', name: 'app_sponsor_show', methods: ['GET'])]
    public function show(Sponsor $sponsor): Response
    {
        return $this->render('sponsor/show.html.twig', [
            'sponsor' => $sponsor,
        ]);
    }

    #[Route('/{idSponsor}/edit', name: 'app_sponsor_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sponsor $sponsor, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SponsorType::class, $sponsor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_sponsor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sponsor/edit.html.twig', [
            'sponsor' => $sponsor,
            'form' => $form,
        ]);
    }

    #[Route('/{idSponsor}/delete', name: 'app_sponsor_delete', methods: ['POST'])]
    public function delete(Request $request, Sponsor $sponsor, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sponsor->getIdSponsor(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($sponsor);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sponsor_index', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/{idSponsor}/events', name: 'app_sponsor_events', methods: ['GET'])]
    public function showSponsorEvents(Sponsor $sponsor, EvenementSponsorRepository $evenementSponsorRepository): Response
    {
        $evenementSponsors = $evenementSponsorRepository->findBy(['sponsor' => $sponsor]);

        return $this->render('sponsor/events.html.twig', [
            'sponsor' => $sponsor,
            'evenement_sponsors' => $evenementSponsors,
        ]);
    }

    #[Route('/suggestions', name: 'app_sponsor_suggestions', methods: ['POST'])]
public function getSuggestions(Request $request, GeminiAIService $geminiAiService): Response
{
    $sector = $request->request->get('sector');
    
    try {
        $prompt = "Génère une liste de 5 sponsors potentiels dans le secteur $sector. 
        Pour chaque sponsor, fournis:
        - Nom complet (data-name)
        - Domaine d'activité
        - Budget moyen estimé en euros (data-budget)
        
        Format de sortie EXCLUSIVEMENT:
        <div class='suggestion-item' data-name='NOM_DU_SPONSOR' data-budget='BUDGET'>
            <h5>NOM_DU_SPONSOR</h5>
            <p><strong>Domaine:</strong> DOMAINE_ACTIVITE</p>
            <p><strong>Budget moyen:</strong> BUDGET €</p>
        </div>
        
        Ne donne aucune explication, commentaire ou texte supplémentaire. Juste les 5 divs comme dans l'exemple.";
        
        $response = $geminiAiService->generateContent($prompt);
        
        // Nettoyer la réponse si nécessaire
        $cleanResponse = strip_tags($response['text'], '<div><h5><p><strong><em><br>');
        
        return new Response($cleanResponse);
    } catch (\Exception $e) {
        return new Response(
            '<div class="alert alert-danger">Erreur lors de la génération des suggestions: '.$e->getMessage().'</div>',
            Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
#[Route('/{idSponsor}/events/pdf', name: 'app_sponsor_events_pdf', methods: ['GET'])]
    public function generateSponsorEventsPdf(
        #[MapEntity(id: 'idSponsor')] Sponsor $sponsor,
        EvenementSponsorRepository $evenementSponsorRepository,
        EvennementRepository $evennementRepository
    ): Response {
        // Récupérez les événements sponsorisés pour ce sponsor
        $evenementSponsors = $evenementSponsorRepository->findBy(['sponsor' => $sponsor]);

        // Créez un nouveau document PDF avec TCPDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Définissez les informations du document
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Votre Nom/Organisation');
        $pdf->SetTitle('Liste des Événements Sponsorisés par ' . $sponsor->getNomSponso() . ' ' . $sponsor->getPrenomSponso());
        $pdf->SetSubject('Événements Sponsorisés');

        // En-tête et pied de page par défaut
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 001', PDF_HEADER_STRING, [0, 64, 255], [0, 64, 128]);
       
        $pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
        $pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);

        // Marges
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // Saut de page automatique
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // Police par défaut
        $pdf->SetFont('helvetica', '', 12);

        // Ajoutez une page
        $pdf->AddPage();

        // Contenu du PDF
        $html = '<h1>Liste des Événements Sponsorisés par ' . $sponsor->getNomSponso() . ' ' . $sponsor->getPrenomSponso() . '</h1>';

        if (!empty($evenementSponsors)) {
            $html .= '<table border="1" cellpadding="5">';
            $html .= '<thead><tr><th>Événement</th><th>Date Début Contrat</th><th>Date Fin Contrat</th><th>Durée</th></tr></thead><tbody>';
            foreach ($evenementSponsors as $evenementSponsor) {
               $dateFin = null;
if ($evenementSponsor->getDatedebutContrat() && $evenementSponsor->getDuree()) {
    $dateDebut = clone $evenementSponsor->getDatedebutContrat(); // Cloner l'objet DateTime
    if ($evenementSponsor->getDuree() === 'troisMois') {
        $dateFin = $dateDebut->modify('+3 months');
    } elseif ($evenementSponsor->getDuree() === 'sixMois') {
        $dateFin = $dateDebut->modify('+6 months');
    } elseif ($evenementSponsor->getDuree() === 'unAns') {
        $dateFin = $dateDebut->modify('+1 year');
    }
}

                $html .= '<tr>';
                $html .= '<td>' . $evenementSponsor->getEvenement()->getNomEvent() . '</td>';
                $html .= '<td>' . ($evenementSponsor->getDatedebutContrat() ? $evenementSponsor->getDatedebutContrat()->format('d/m/Y') : 'N/A') . '</td>';
                $html .= '<td>' . ($dateFin ? $dateFin->format('d/m/Y') : 'N/A') . '</td>';
                $html .= '<td>';
                if ($evenementSponsor->getDuree() === 'troisMois') {
                    $html .= '3 Mois';
                } elseif ($evenementSponsor->getDuree() === 'sixMois') {
                    $html .= '6 Mois';
                } elseif ($evenementSponsor->getDuree() === 'unAns') {
                    $html .= '1 An';
                } else {
                    $html .= 'N/A';
                }
                $html .= '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';
        } else {
            $html .= '<p>Aucun événement sponsorisé trouvé pour ce sponsor.</p>';
        }

        // Output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');

        // Output the PDF to the browser
        $pdf->Output('evenements_sponsorises_' . $sponsor->getNomSponso() . '.pdf', 'I');

        // Vous devez retourner une Response, même si TCPDF envoie déjà les headers
        return new Response();
    }
}