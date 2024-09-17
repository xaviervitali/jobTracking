<?php

namespace App\Controller;

use App\Entity\Action;
use App\Entity\Job;
use App\Entity\JobTracking;
use App\Form\ActionType;
use App\Form\JobFormType;
use App\Repository\JobRepository;
use App\Repository\JobTrackingRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class HomeController extends AbstractController
{
    #[Route('/synthese', name: 'app_synthese')]
    public function synthese(
        JobTrackingRepository $jobTracking,
        Security $security
    ): Response {
        $user = $security->getUser();
        $jobsInProgress = $jobTracking->findJobsByUserOrderedByDate($user);

        return $this->render('home/index.html.twig', [
            'user' => $user,
            'jobsInProgress' =>   $jobsInProgress

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
}
