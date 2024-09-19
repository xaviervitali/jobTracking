<?php

namespace App\Controller;

use App\Entity\JobTracking;
use App\Form\JobTrackingType;
use App\Repository\JobTrackingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/job/tracking')]
final class JobTrackingControllerCRUD extends AbstractController
{
    #[Route(name: 'app_job_tracking_index', methods: ['GET'])]
    public function index(JobTrackingRepository $jobTrackingRepository): Response
    {
        return $this->render('job_tracking/index.html.twig', [
            'job_trackings' => $jobTrackingRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_job_tracking_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $jobTracking = new JobTracking();
        $form = $this->createForm(JobTrackingType::class, $jobTracking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($jobTracking);
            $entityManager->flush();

            return $this->redirectToRoute('app_job_tracking_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('job_tracking/new.html.twig', [
            'job_tracking' => $jobTracking,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_job_tracking_show', methods: ['GET'])]
    public function show(JobTracking $jobTracking): Response
    {
        return $this->render('job_tracking/show.html.twig', [
            'job_tracking' => $jobTracking,
        ]);
    }



    #[Route('/{id}', name: 'app_job_tracking_delete', methods: ['POST'])]
    public function delete(Request $request, JobTracking $jobTracking, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$jobTracking->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($jobTracking);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_job_tracking_index', [], Response::HTTP_SEE_OTHER);
    }
}
