<?php

namespace App\Controller;

use App\Entity\Job;
use App\Entity\JobTracking;
use App\Enums\ActionStatus;
use App\Form\JobFormType;
use App\Repository\ActionRepository;
use App\Repository\JobRepository;
use App\Repository\JobSourceRepository;
use App\Repository\JobTrackingRepository;
use App\Repository\UserRepository;
use App\Service\JobService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Smalot\PdfParser\Parser;

final class JobController extends AbstractController
{
    #[Route('/tableau_de_bord', name: 'app_job_index', methods: ['GET'])]
    public function index(JobRepository $jobRepository,  ActionRepository $actionRepository, Security $security, JobSourceRepository $jobSourceRepository): Response
    {

        $user = $security->getUser();

        $date = new \DateTime();
        $date->modify('-1 year');

        $date = DateTimeImmutable::createFromMutable($date);

        $jobService = new JobService($user, $date, $jobRepository);

        $jobsPerMonths = $jobService->getJobsPerMonth();
        $closedJobsPerMonth = $jobService->getClosedJobsPerMonth();
        $jobSources = $jobRepository->getJobSourceCountByUser($user);
        $currentWeekJob = $jobService->getCurrentWeekJobs($user);
        $jobActions = $actionRepository->getActionCountAndRatioByUser($user);
        $actionsBySourceCount = $jobSourceRepository->getActionsNameAndCountByJobSource($user);


        return $this->render('job/index.html.twig', [
            'jobsPerMonths' => $jobsPerMonths,
            'closedJobsPerMonth' => $closedJobsPerMonth,
            'jobSources' => $jobSources,
            'jobActions' => $jobActions,
            'actionsBySourceCount' => $actionsBySourceCount,
            'currentWeekJob' => $currentWeekJob,

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


    #[Route('/motivation_text', name: 'motivation_text')]
    public function motivationText(Security $security, UserRepository $userRepository)
    {
        $user = $security->getUser();
        $prompt = 'Génère une lettre de motivation.';

        if ($user) {
            $user = $userRepository->findOneBy(['email' => $user->getUserIdentifier()]);
        }

        $parser = new Parser();
        $pdf = $parser->parseFile('uploads/cv/' . $user->getCvs()[0]->getCVName());
        $text = $pdf->getText();
        $client = new \GuzzleHttp\Client();
        $response = null;

        try {
            $response = $client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => "Bearer " . $_ENV['API_KEY'],
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => "$prompt. Voici mon CV :.$text"
                            // 'content' => "Génère une lettre de motivation pour un poste de Développeur Web. Voici mon CV : [INFORMATIONS DU CV] et voici l'offre d'emploi : [DETAILS DE L'OFFRE]."
                        ]
                    ],
                    'max_tokens' => 300,
                ],
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 429) {
                // Attendre avant de réessayer
                sleep(10); // Ajustez ce délai selon vos besoins
                // Réessayez la requête ici
                
            }
        }

        return $this->json($response);
    }
}
