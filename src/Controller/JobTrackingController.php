<?php

namespace App\Controller;

use App\Entity\Action;
use App\Entity\Job;
use App\Entity\JobTracking;
use App\Entity\Note;
use App\Enums\ActionStatus;
use App\Form\ActionType;
use App\Form\JobFormType;
use App\Form\JobTrackingType;
use App\Form\NoteType;
use App\Repository\ActionRepository;
use App\Repository\JobRepository;
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

    #[Route('/candidature/{id}', name: 'app_job_tracking')]
    public function candidature(Job $job, Security $security, EntityManagerInterface $em, Request $request, JobTrackingRepository $jobTrackingRepository, ActionRepository $actionRepository, JobRepository $jobRepository)
    {
        $user = $security->getUser();
        if ($job->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier ce job.');
        }

        $formJob = $this->createForm(JobFormType::class, $job);
        $formJob->handleRequest($request);


        if ($formJob->isSubmitted() && $formJob->isValid()) {
            $newJob =  $formJob->getData();
            $firstActionId = $actionRepository->findOneBy(['name' => ActionStatus::getStartActionName()]);

            $firstJobTracking = $jobTrackingRepository->findOneBy(['action' => $firstActionId, 'job'=>$newJob]);
            $firstJobTracking->setCreatedAt($newJob->getCreatedAt());

            
            $em->persist($firstJobTracking);
            $em->persist($newJob);
            $em->flush();

            $this->addFlash("info", "Annonce modifiée");
        }

        $formAction = $this->createForm(ActionType::class);

        $jobTracking = new JobTracking();
        $formJobTracking = $this->createForm(JobTrackingType::class, $jobTracking);

        $note = new Note();
        $formNote = $this->createForm(NoteType::class, $note, ['job' => $job]);

        $jobTrackings =  $job->getJobTracking()->toArray();
        usort($jobTrackings, function ($a, $b) {
            return $a->getCreatedAt() <=> $b->getCreatedAt();
        });
        return $this->render('job_tracking/index.html.twig', [
            'form' => $formJob,
            'formNote' => $formNote,
            'formAction' => $formAction,
            'formJobTracking' => $formJobTracking,
            'job' => $job,
            'jobTrackings' => $jobTrackings,
            'isClosedJob' => $jobRepository->isClosedJob($job)
        ]);
    }

    #[Route('/new_job_tracking/{id}', name: 'app_new_job_tracking', methods: ['GET', 'POST'])]
    public function new(Job $job, Request $request, EntityManagerInterface $entityManager, Security $security)
    {
        $jobTracking = new JobTracking();


        $actionId = $entityManager->getRepository(Action::class)->find($request->request->all()['action']['name']);
        // $formAction->handleRequest($request);
        if (!$actionId) {
            throw $this->createNotFoundException('Action ID not found in request');
        }

        $action = $entityManager->getRepository(Action::class)->find($actionId);

        if ($job->getUser() == $security->getUser()) {
            $jobTracking->setJob($job)
                ->setUser($security->getUser())
                ->setCreatedAt(new DateTimeImmutable())
                ->setAction($action)
            ;

            $entityManager->persist($jobTracking);
            $entityManager->flush();
        }

        // return $this->redirectToRoute('app_job_tracking', ['id' => $job->getId()], Response::HTTP_SEE_OTHER);
        return $this->redirectToRoute('app_synthese',  [], Response::HTTP_SEE_OTHER);
    }





    #[Route('job_tracking/{id}/edit', name: 'app_job_tracking_edit', methods: ['GET', 'POST'])]
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


    #[Route('job_tracking/{id}/delete', name: 'app_job_tracking_delete', methods: ['GET', 'POST'])]
    public function delete(JobTracking $jobTracking, EntityManagerInterface $entityManager, Request $request, JobTrackingRepository $jobTrackingRepository, Security $security): Response
    {
        $currentJobTracking = $jobTrackingRepository->findOneBy(['id' => $jobTracking]);

        if ($currentJobTracking->getUser() !== $security->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier ce job.');
        }

        if ($currentJobTracking && $currentJobTracking->getUser() === $security->getUser()) {
            $entityManager->remove($currentJobTracking);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_synthese',  [], Response::HTTP_SEE_OTHER);

    }
}
