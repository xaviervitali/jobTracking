<?php

namespace App\Controller;

use App\Repository\JobRepository;
use DateTime;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
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

        $jobsInProgress = array_map(function($jobInProgress){return array_slice($jobInProgress, 1);}, $jobsInProgressByUser);
        
        return $this->render('home/index.html.twig', [
             'jobsInProgressJson' => json_encode($jobsInProgress),
        ]);
    }



}
