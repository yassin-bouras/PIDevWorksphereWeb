<?php

namespace App\Controller;

use App\Entity\Entretien;
use App\Entity\Feedback;
use App\Form\FeedbackType;
use App\Repository\EntretienRepository;
use App\Repository\FeedbackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/feedback')]
final class FeedbackController extends AbstractController{
    #[Route(name: 'app_feedback_index', methods: ['GET'])]
    public function index(FeedbackRepository $feedbackRepository): Response
    {
        return $this->render('feedback/index.html.twig', [
            'feedback' => $feedbackRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_feedback_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $feedback = new Feedback();
        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($feedback);
            $entityManager->flush();

            return $this->redirectToRoute('app_feedback_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('feedback/new.html.twig', [
            'feedback' => $feedback,
            'form' => $form,
        ]);
    }

    #[Route('/feedback/news/{id}', name: 'app_feedback_news')]
    public function news(int $id, Request $request, EntityManagerInterface $entityManager , EntretienRepository $entretienRepository): Response
    {
        $entretien = $entityManager->getRepository(Entretien::class)->find($id);

         
        if (!$entretien) {
            throw $this->createNotFoundException('Entretien non trouvé');
        }

        if ($entretien->getFeedback()) {
            $this->addFlash('warning', 'Cet entretien a déjà un feedback.');
            return $this->redirectToRoute('app_entretien_show', ['id' => $id]);
        }

        $feedback = new Feedback();
        $feedback->setDate_feedback(new \DateTime());
        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $feedback1 = $form->getData();

            $values = [
                $form->get('q1')->getData(),
                $form->get('q2')->getData(),
                $form->get('q3')->getData(),
                $form->get('q4')->getData(),
                $form->get('q5')->getData(),
            ];
            $average = array_sum($values) / count($values);
            $rate = round($average * 5, 2); 
        
            $feedback->setRate($rate);

            $feedback->setEntretien($entretien);
            $entretien->setFeedback($feedback);
            $entityManager->persist($feedback);
            $entityManager->flush();

            $this->addFlash('success', 'Feedback ajouté avec succès !');
            return $this->redirectToRoute('entretien_by_employee');
        


        }

        return $this->render('feedback/new.html.twig', [
            'form' => $form->createView(),
            'entretien' => $entretien,
        ]);

        // return $this->render('entretien/index.html.twig', [
        //     'entretiens' => $entretienRepository->findAll(),
        // ]);
    }




    #[Route('/{id}', name: 'app_feedback_show', methods: ['GET'])]
    public function show(Feedback $feedback): Response
    {
        return $this->render('feedback/show.html.twig', [
            'feedback' => $feedback,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_feedback_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Feedback $feedback, EntityManagerInterface $entityManager, EntretienRepository $entretienRepository): Response
    {

        $feedback->setDate_feedback(new \DateTime());
        $form = $this->createForm(FeedbackType::class, $feedback);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            


            $entityManager->flush();

            $this->addFlash('success', 'Feedback ajouté avec succès !');

            return $this->redirectToRoute('entretien_by_employee');

            
        }

        return $this->render('feedback/edit.html.twig', [
            'feedback' => $feedback,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_feedback_delete', methods: ['POST'])]
    public function delete(Request $request, Feedback $feedback, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$feedback->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($feedback);
            $entityManager->flush();
        }

        return $this->redirectToRoute('entretien_by_employee', [], Response::HTTP_SEE_OTHER);
    }
}
