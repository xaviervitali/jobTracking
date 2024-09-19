<?php

namespace App\Controller;

use App\Entity\Action;
use App\Entity\Job;
use App\Entity\JobTracking;
use App\Entity\Note;
use App\Form\ActionType;
use App\Form\JobFormType;
use App\Form\JobTrackingType;
use App\Form\NoteType;
use App\Repository\JobRepository;
use App\Repository\JobTrackingRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/synthese', name: 'app_synthese')]
    public function synthese(
        JobTrackingRepository $jobTrackingRepository,
        JobRepository $jobRepository,
        Security $security
    ): Response {
        $user = $security->getUser();
        $jobsInProgress = $jobTrackingRepository->findJobsByUserOrderedByDate($user);
        $months = [];
        $jobs= $jobRepository->findBy(['user'=>$user]);


        
        dd($jobs);
        return $this->render('home/index.html.twig', [
            'user' => $user,
            'jobsInProgress' =>   $jobsInProgress

        ]);
    }

    #[Route('/candidature/{id}', name: 'app_job_tracking')]
    public function candidature(Job $job, Security $security, EntityManagerInterface $em, Request $request)
    {
        $user = $security->getUser();
        if ($job->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier ce job.');
        }

        $formJob = $this->createForm(JobFormType::class, $job);
        $formJob->handleRequest($request);


        if ($formJob->isSubmitted() && $formJob->isValid()) {
            $newJob =  $formJob->getData();
            $em->persist($newJob);

            $em->persist($newJob);
            $em->flush();

            $this->addFlash("info", "Annonce modifiée");

        }

        $formAction = $this->createForm(ActionType::class);

        $jobTracking = new JobTracking();
        $formJobTracking = $this->createForm(JobTrackingType::class, $jobTracking);

        $note = new Note();
        $formNote = $this->createForm(NoteType::class, $note);
        
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
            'jobTrackings' => $jobTrackings
        ]);
    }


    #[Route('/candidature/{id}/delete', name: 'candidature_delete')]
    public function candidatureDelete(Job $job, EntityManagerInterface $em,  Security $security)
    {
        $user = $security->getUser();
        if ($job->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier ce job.');
        }
        $recruiter = $job->getRecruiter();
        $title = $job->getTitle();
        $em->remove($job);
        $em->flush();
        $this->addFlash("info", "Candidature chez $recruiter ($title) supprimée");
        return $this->redirectToRoute('app_synthese');
    }
    #[Route('/candidature/{id}/edit', name: 'candidature_edit')]
    public function candidatureEdit(Job $job, EntityManagerInterface $entityManager,  Request $request, Security $security)
    {
        $form = $this->createForm(JobFormType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

        }
        return $this->redirectToRoute('app_job_tracking', ['id'=>$job->getId()]);
    }

}
