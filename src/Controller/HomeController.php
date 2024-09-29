<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\JobRepository;
use DateTime;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

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

        
        return $this->render('home/index.html.twig', [
             'jobsInProgressJson' => json_encode($jobsInProgressByUser),
        ]);
    }


    #[Route('/mon_espace', name: 'app_user_show', methods: ['GET'])]
    public function show(JobRepository $jobRepository, SerializerInterface $serializer, Security $security): Response
    {
        $user = $security->getUser();
        $userJobs = $jobRepository->findByUser($user);

        $jsonContent = $serializer->serialize($userJobs, 'json', [
            'groups' => ['job'],

        ]);

        return $this->render('home/my-space.html.twig', [
           'jobs'=> $jsonContent,
            'user' => $user,
        ]);
    }

}
