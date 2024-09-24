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
use App\Service\JobService;
use App\Service\JobTrackingService;
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
        Security $security,
     
    ): Response {
        $user = $security->getUser();
        $date = new DateTime();
        $date->modify('-1 year');
        $date = DateTimeImmutable::createFromMutable($date);

        $jobsInProgressByUser = $jobRepository->findJobsInProgressOrClosedByUser($user);

        $jobs= array_map(function($jobInProgress){return $jobInProgress[0];}, $jobsInProgressByUser);

        $jobsInProgress = array_map(function($jobInProgress){return array_slice($jobInProgress, 1);}, $jobsInProgressByUser);

        $jobTrackingService = new JobTrackingService();
        $jobData = $jobTrackingService->getActionsCountForJobs($jobs);
 
        return $this->render('home/index.html.twig', [
            'jobsInProgress' => $jobsInProgress,
             'jobsData' => $jobData,
             'jobsInProgressJson' => json_encode($jobsInProgress),

        ]);
    }

}
