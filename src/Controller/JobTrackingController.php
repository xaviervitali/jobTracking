<?php

namespace App\Controller;

use App\Entity\Action;
use App\Entity\Job;
use App\Entity\JobTracking;
use App\Form\JobTrackingType;
use App\Repository\JobTrackingRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class JobTrackingController extends AbstractController
{

    #[Route('/new_job_tracking/{id}', name: 'app_new_job_tracking', methods: ['GET', 'POST'])]
    public function new(Job $job, Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $jobTracking = new JobTracking();


        $actionId = $entityManager->getRepository(Action::class)->find($request->request->all()['action']['name']);
        // $formAction->handleRequest($request);
        if (!$actionId) {
            throw $this->createNotFoundException('Action ID not found in request');
        }
        $action = $entityManager->getRepository(Action::class)->find($actionId);

        if ( $job->getUser() == $security->getUser()) {
            $jobTracking->setJob($job)
                ->setUser($security->getUser())
                ->setCreatedAt(new DateTimeImmutable())
                ->setAction($action)
            ;

            $entityManager->persist($jobTracking);
            $entityManager->flush();

        }

        return $this->redirectToRoute('app_job_tracking', ['id'=>$job->getId()]);
    }

    // #[Route('new_job_tracking/', name: 'app_new_job_tracking')]
    // function newJobTracking(Request $request, EntityManagerInterface $entityManager, Security $security)
    // {


    //     $actionId = $entityManager->getRepository(Action::class)->find($request->request->get('actionId'));

    //     // $formAction->handleRequest($request);
    //     if (!$actionId) {
    //         throw $this->createNotFoundException('Action ID not found in request');
    //     }

    //     // Récupérer l'entité Action à partir de l'identifiant
    //     $action = $entityManager->getRepository(Action::class)->find($actionId);

    //     if (!$action) {
    //         throw $this->createNotFoundException('Action not found');
    //     }

    //     $jobId = $entityManager->getRepository(Job::class)->find($request->request->get('jobId'));

    //     // $formAction->handleRequest($request);
    //     if (!$jobId) {
    //         throw $this->createNotFoundException('Job ID not found in request');
    //     }

    //     // Récupérer l'entité Action à partir de l'identifiant
    //     $job = $entityManager->getRepository(Job::class)->find($jobId);

    //     if (!$job) {
    //         throw $this->createNotFoundException('Action not found');
    //     }

    //     if ($job->getUser() !== $security->getUser()) {
    //         throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier ce job.');
    //     }

    //     $jobTracking = new JobTracking();
    //     $jobTracking
    //         ->setJob($job)
    //         ->setAction($action)
    //         ->setUser($security->getUser())
    //         ->setCreatedAt(new DateTimeImmutable());
    //     $entityManager->persist($jobTracking);
    //     $entityManager->flush();
    //     $this->addFlash("info", "Action " . $action->getName() . " ajoutée");


    //     return $this->json('Ok');
    // }




    #[Route('action/{id}/edit', name: 'app_job_tracking_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, JobTracking $jobTracking, EntityManagerInterface $entityManager, JobTrackingRepository $jobTrackingRepository): Response
    {

        $jobTrackings = array_filter(
            $jobTrackingRepository->findBy(['job' => $jobTracking->getJob()]),
            function ($jobTrackingItem) use ($jobTracking) {
                return $jobTrackingItem->getId() !== $jobTracking->getId();
            }
        );

        usort($jobTrackings, function ($a, $b) {
            return $a->getCreatedAt() <=> $b->getCreatedAt();
        });

        $form = $this->createForm(JobTrackingType::class, $jobTracking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            return $this->redirectToRoute('app_job_tracking', ['id' => $jobTracking->getJob()->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('job_tracking/edit.html.twig', [
            'job_tracking' => $jobTracking,
            'formJobTracking' => $form,
            'jobTrackings' => $jobTrackings
        ]);
    }
}
