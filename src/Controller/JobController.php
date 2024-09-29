<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\JobTracking;
use App\Enums\ActionStatus;
use App\Form\JobFormType;
use App\Repository\ActionRepository;
use App\Repository\JobRepository;
use App\Repository\JobTrackingRepository;
use App\Service\JobService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class JobController extends AbstractController
{
    #[Route('/tableau_de_bord', name: 'app_job_index', methods: ['GET'])]
    public function index(JobRepository $jobRepository,  ActionRepository $actionRepository, Security $security): Response
    {
        
        $user =$security->getUser();
   
        $date = new \DateTime();
        $date->modify('-1 year');

        $date = DateTimeImmutable::createFromMutable($date);

        $jobService =new JobService($user, $date, $jobRepository);
        
        $jobsPerMonths = $jobService->getJobsPerMonth();
        $closedJobsPerMonth = $jobService->getClosedJobsPerMonth();
        $jobSources = $jobRepository->getJobSourceCountByUser($user);
        $jobActions = $actionRepository->getActionCountAndRatioByUser($user);


        return $this->render('job/index.html.twig', [
           'jobsPerMonths'=>$jobsPerMonths,
           'closedJobsPerMonth'=>$closedJobsPerMonth,
           'jobSources'=>$jobSources,
           'jobActions'=>$jobActions,

        ]);
    }

    #[Route('/nouvelle_candidature', name: 'candidature_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security, ActionRepository $actionRepository): Response
    {
        $job = new Job();
        $form = $this->createForm(JobFormType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newAction = $actionRepository->findOneBy(['name' => ActionStatus::getStartActionName()]);
            $user = $security->getUser();

            $job->setUser($user);
            $entityManager->persist($job);

            $jobTracking = new JobTracking();
            $jobTracking->setAction($newAction)
                ->setJob($job)
                ->setCreatedAt($job->getCreatedAt())
                ->setUser($user);
            $entityManager->persist($jobTracking);

            $entityManager->flush();

            return $this->redirectToRoute('app_synthese', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('job/new.html.twig', [
            'job' => $job,
            'form' => $form,
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
    public function candidatureEdit(Job $job, EntityManagerInterface $entityManager,  Request $request, Security $security, JobTrackingRepository $jobTrackingRepository)
    {
        $form = $this->createForm(JobFormType::class, $job);
        $form->handleRequest($request);
        $user = $security->getUser();
        if ($job->getUser() !== $user) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier ce job.');
        }
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_job_tracking', ['id' => $job->getId()]);
    }
}
