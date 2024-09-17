<?php

namespace App\Controller;

use App\Entity\Action;
use App\Entity\Job;
use App\Entity\JobTracking;
use App\Form\ActionType;
use App\Form\JobFormType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class JobTrackingController extends AbstractController
{

    #[Route('newJobTracking/', name: 'newJobTracking')]
    function newJobTracking(Request $request, EntityManagerInterface $entityManager, Security $security)
    {


        $actionId = $entityManager->getRepository(Action::class)->find($request->request->get('actionId'));

        // $formAction->handleRequest($request);
        if (!$actionId) {
            throw $this->createNotFoundException('Action ID not found in request');
        }

        // Récupérer l'entité Action à partir de l'identifiant
        $action = $entityManager->getRepository(Action::class)->find($actionId);

        if (!$action) {
            throw $this->createNotFoundException('Action not found');
        }

        $jobId = $entityManager->getRepository(Job::class)->find($request->request->get('jobId'));

        // $formAction->handleRequest($request);
        if (!$jobId) {
            throw $this->createNotFoundException('Job ID not found in request');
        }

        // Récupérer l'entité Action à partir de l'identifiant
        $job = $entityManager->getRepository(Job::class)->find($jobId);

        if (!$job) {
            throw $this->createNotFoundException('Action not found');
        }

        if ($job->getUser() !== $security->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à modifier ce job.');
        }
        $jobTracking = new JobTracking();
        $jobTracking
            ->setJob($job)
            ->setAction($action)
            ->setUser($security->getUser())
            ->setCreatedAt(new DateTimeImmutable());
        $entityManager->persist($jobTracking);
        $entityManager->flush();
        $this->addFlash("info", "Action ". $action->getName()." ajoutée");


        return $this->json('Ok');
    }

    
    #[Route('/candidature/{id}', name: 'candidature')]
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

            return $this->json('Ok');
        }

        $formAction = $this->createForm(ActionType::class);

        return $this->render('job_tracking/edit.html.twig', [
            'form' => $formJob,
            'formAction' => $formAction,
            'job' => $job
        ]);
    }
}
