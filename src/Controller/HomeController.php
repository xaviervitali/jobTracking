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
use DateTime;
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
        JobRepository $jobRepository,
        Security $security
    ): Response {
        $user = $security->getUser();
        $jobsInProgress = [];
        $closedJobs = [];
        $date = new DateTime();
        $date->modify('-1 year');
        $date = DateTimeImmutable::createFromMutable($date);

        $jobs = $jobRepository->findByUserAndMoreThanDate( $user, $date);

        foreach ($jobs as $job) {
           $isClosed = count (array_filter(
                $job->getJobTracking()->toArray(),
                function (JobTracking $jobTracking) {
                    return !!$jobTracking->getAction()->isSetClosed();
                }
            )) > 0;

            if($isClosed){
                $closedJobs[] = $job;
            } else {
                $jobsInProgress[] = $job;
            }

        }

        $dates = array_unique(array_map(function (Job $job) {
            return $job->getCreatedAt()->format('Y-m');
        }, $jobs));
        sort($dates);


        $completeDates =    $this->addIntermediatedatesToNow($dates[0]);


        $jobsPerMonth = [];
        $closedJobsPerMonth = [];
        foreach ($completeDates as $month) {
            $jobsPerMonth[$month] = count(array_filter(
                $jobs,
                function (Job $job) use ($month) {
                    return $job->getCreatedAt()->format('Y-m') == $month;
                }
            ));

            $closedJobsPerMonth[$month] = count(array_filter(
                $closedJobs,
                function (Job $job) use ($month) {
                    return $job->getCreatedAt()->format('Y-m') == $month;
                }
            ));
        }






        return $this->render('home/index.html.twig', [
            'jobsInProgress' => $jobsInProgress,
            'jobsData' => array_values($jobsPerMonth),
            'responsesData' => array_values($closedJobsPerMonth),
            'categories' => array_keys($jobsPerMonth),

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
        return $this->redirectToRoute('app_job_tracking', ['id' => $job->getId()]);
    }

    private function addIntermediatedatesToNow($startDate)
    {
        $completeDates = [];
        $start = new DateTime($startDate);
        $end = new DateTime();

        // Ajoute tous les mois entre les deux dates, y compris la date de début
        while ($start <= $end) {
            $completeDates[] = $start->format('Y-m');
            $start->modify('+1 month'); // Ajoute un mois
        }
        return $completeDates;
    }
}
