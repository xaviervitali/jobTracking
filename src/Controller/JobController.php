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
use App\Service\ApiService;
use App\Service\JobService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class JobController extends AbstractController
{
    #[Route('/tableau_de_bord', name: 'app_job_index', methods: ['GET'])]
    public function index(JobRepository $jobRepository, ActionRepository $actionRepository, Security $security, JobSourceRepository $jobSourceRepository): Response
    {

        $user = $security->getUser();

        $date = new \DateTime();
        $date->modify('-1 year');

        $date = DateTimeImmutable::createFromMutable($date);

        $jobService = new JobService($user, $date, $jobRepository);

        $jobsPerMonths = $jobService->getJobsPerMonth();
        $closedJobsPerMonth = $jobService->getClosedJobsPerMonth();
        $jobSources = $jobRepository->getJobSourceCountByUser($user);
        $currentWeekJob = $jobService->getCurrentWeekJobs();
        $jobActions = $actionRepository->getActionCountAndRatioByUser($user);
        $jobClosedActions = $actionRepository->getActionCountAndRatioByUser($user, true);
        $actionsBySourceCount = $jobSourceRepository->getActionsNameAndCountByJobSource($user);


        return $this->render('job/index.html.twig', [
            'jobsPerMonths' => $jobsPerMonths,
            'closedJobsPerMonth' => $closedJobsPerMonth,
            'jobSources' => $jobSources,
            'jobActions' => $jobActions,
            'actionsBySourceCount' => $actionsBySourceCount,
            'currentWeekJob' => $currentWeekJob,
            'jobClosedActions' => $jobClosedActions,
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
    public function candidatureDelete(Job $job, EntityManagerInterface $em, Security $security)
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
    public function candidatureEdit(Job $job, EntityManagerInterface $entityManager, Request $request, Security $security)
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


    #[Route('/job_alert', name: 'app_job_alert')]
    public function jobAlert(Security $security, UserRepository $userRepository, CacheInterface $cache, ApiService $apiService): Response
    {
        $user = $security->getUser();


        // Récupération des paramètres de l'utilisateur
        $user = $userRepository->findOneBy(['email' => $user->getUserIdentifier()]);
        $apiSettings = $user->getJobSearchSettings();

        if (empty($apiSettings)) {
            $this->addFlash("info", "Vous n'avez pas encore créé de profil de recherche.");
            return $this->redirectToRoute('app_user_show');
        }
        // Créer le serializer
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);
        // Sérialiser l'entité
        $params = $serializer->normalize($apiSettings, null, ['groups' => ['apiSettingsGroup']]);

        $adzunaParams = [
            'app_id' => $_ENV['ADZUNA_API_ID'],     // ID de l'API
            'app_key' => $_ENV['ADZUNA_API_KEY'],   // Clé de l'API
            'results_per_page' => 50,               // Nombre de résultats par page
            'what' => $apiSettings->getWhat(),      // Mot clé de recherche
            'where' => $apiSettings->getCity(),     // Localisation
            'what_exclude' => $apiSettings->getWhatExclude(),
            'sort_by' => 'date',
            'distance' => $apiSettings->getDistance(),
            'max_days_old' => 7 // Exclusion de certains mots-clés
        ];


        $franceTravailParams = [
            'motsCles' => $apiSettings->getWhat(),      // Mot clé de recherche
            'where' => $apiSettings->getCity(),     // Localisation
            'what_exclude' => $apiSettings->getWhatExclude(),
            'distance' => $apiSettings->getDistance(),
            'publieeDepuis' => 7 // Exclusion de certains mots-clés
        ];

        // Calcul du nombre de secondes jusqu'à minuit
        $now = new \DateTime();
        $midnight = new \DateTime('tomorrow midnight');
        $secondsUntilMidnight = $midnight->getTimestamp() - $now->getTimestamp();

        // Générer une clé de cache unique pour chaque utilisateur
        $cacheKey = 'adzuna_api_' . $user->getId();

        // Récupération des données en cache ou appel à l'API si nécessaire
        $cachedData = $cache->get($cacheKey, function (ItemInterface $item) use ($secondsUntilMidnight, $apiService, $adzunaParams, $franceTravailParams, $apiSettings, $params) {
            $item->expiresAfter($secondsUntilMidnight); // Expiration à minuit

            // Appel à l'API via ApiService et retourne les données avec les paramètres actuels
            $adzunaJobResponseData = $apiService->getAdzunaJobs($adzunaParams, $apiSettings->getCountry());

            $franceTravailJobResponseData = $apiService->getFranceTravailJobs($franceTravailParams);
            // Retourner les paramètres et la réponse de l'API
            return [
                'params' =>  $params,
                'response' => ['azduna' => $adzunaJobResponseData, 'franceTravail' => $franceTravailJobResponseData],
            ];
        });

        // Comparer les paramètres actuels avec ceux en cache
        if (isset($cachedData['params']) && $cachedData['params'] !== $params) {
            // Si les paramètres ont changé, faire une nouvelle requête API
            $cachedData = [
                'params' => $params,
                'response' => ['azduna' => $apiService->getAdzunaJobs($adzunaParams, $apiSettings->getCountry()), 'franceTravail' => $apiService->getFranceTravailJobs($franceTravailParams)]
            ];

            // Pas besoin de mettre à jour manuellement le cache : la prochaine requête appellera automatiquement l'API si nécessaire
        }

        // Récupérer la réponse des données en cache
        $jobResponseData = $cachedData['response'];
        $adzunaJobResults = json_encode($jobResponseData['azduna']['results']);
        $franceTravailJobResults = json_encode($jobResponseData['franceTravail']);

        // Rendu du template avec les résultats
        return $this->render('job/job_alert.html.twig', [
            'adzunaJobResults' => $adzunaJobResults,
            'franceTravailJobResults' => $franceTravailJobResults,
        ]);
    }
}
