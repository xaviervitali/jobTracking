<?php

namespace App\Controller;

use App\Repository\JobRepository;
use App\Repository\JobTrackingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SummaryController extends AbstractController
{
    #[Route('/candidatures', name: 'app_candidatures')]
    public function index(JobRepository $jobRepository, Security $security): Response
    {
        $userJobs = $jobRepository->findBy(['user' => $security->getUser()]);

        $noAction = 'Envoi candidature';
        $actions = [ $noAction => 0];

        foreach($userJobs as$userJob){
            if(empty($userJob->getJobTracking())){
                $actions[$noAction]++;
                continue;
            }
            
        }

        return $this->render('summary/index.html.twig', [
            'controller_name' => 'SummaryController',
        ]);
    }
}
