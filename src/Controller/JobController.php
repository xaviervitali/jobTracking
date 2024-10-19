<?php

namespace App\Controller;

use App\Entity\Action;
use App\Entity\Job;
use App\Entity\JobSource;
use App\Entity\JobTracking;
use App\Enums\ActionStatus;
use App\Form\JobFormType;
use App\Repository\ActionRepository;
use App\Repository\JobRepository;
use App\Repository\JobSourceRepository;
use App\Repository\UserRepository;
use App\Service\ApiService;
use App\Service\JobService;
use DateTime;
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
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;

final class JobController extends AbstractController
{
    public function __construct(){
        date_default_timezone_set('Europe/Paris');
    }

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
        $closedAvgDelai = $jobRepository->getClosedAvgDelai($user);
        $longuestDelai = $jobRepository->getLonguestDelai($user);
        $mostProlificWeekDay = $jobRepository->getMostProlificWeekDay($user);
        $mostProlificDay = $jobRepository->getMostProlificDay($user);


        return $this->render('job/index.html.twig', [
            'jobsPerMonths' => $jobsPerMonths,
            'closedJobsPerMonth' => $closedJobsPerMonth,
            'jobSources' => $jobSources,
            'jobActions' => $jobActions,
            'actionsBySourceCount' => $actionsBySourceCount,
            'currentWeekJob' => $currentWeekJob,
            'jobClosedActions' => $jobClosedActions,
            'closedAvgDelai' => $closedAvgDelai,
            'longuestDelai' => $longuestDelai,
            'mostProlificDay' => $mostProlificDay,
            'mostProlificWeekDay' => $mostProlificWeekDay,
        ]);
    }


    #[Route('/candidature/job_alert', name: 'candidature_from_job_alert')]
    function candidatureFromJobAlert(Request $request, EntityManagerInterface $entityManager, Security $security)
    {
       // Récupérer le contenu JSON de la requête
       $data = json_decode($request->getContent(), true); // Décoder le JSON en tableau associatif

       // Vérifiez ce que vous recevez
        $jobData = $data['job'];
        
        if (empty($jobData['title']) || empty($jobData['company']) || empty($jobData['description'])) {
            return $this->json(false);
        }
        $date = new DateTimeImmutable();
        $job = new Job();
        $job->setTitle($jobData['title'])
            ->setRecruiter($jobData['company'])
            ->setOfferDescription($jobData['description'])
            ->setCreatedAt($date)
            ->setSource($entityManager->getRepository(JobSource::class)->findOneBy(['name' => $jobData['source']]))
            ->setUser($security->getUser());
        $entityManager->persist($job);
        $jobTracking = new JobTracking();
        $jobTracking
        ->setJob($job)
        ->setCreatedAt($date)
        ->setAction($entityManager->getRepository(Action::class)->findOneBy(['name' => ActionStatus::getStartActionName()]));
        $entityManager->persist($jobTracking);
       
        $entityManager->flush();
        
        return $this->json(true);

    }



    #[Route('/nouvelle_candidature', name: 'candidature_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security, ActionRepository $actionRepository): Response
    {
        $job = new Job();
        $user = $security->getUser();

        $form = $this->createForm(JobFormType::class, $job, [
            'user' => $user,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newAction = $actionRepository->findOneBy(['name' => ActionStatus::getStartActionName()]);

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
        $classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
        $normalizers = new ObjectNormalizer($classMetadataFactory);
        $serializer = new Serializer([$normalizers]);
        // Sérialiser l'entité
        $params = $serializer->normalize($apiSettings, null, ['groups' => ['apiSettingsGroup']]);
        $maxOldDays = intval($apiSettings->getMaxDaysOld()) ?? 8;
        $adzunaParams = [
            // Clé de l'API
            'results_per_page' => 50,               // Nombre de résultats par page
            'what' => $apiSettings->getWhat(),      // Mot clé de recherche
            'where' => $apiSettings->getCity()->getZipCode(),     // Localisation
            'what_exclude' => $apiSettings->getWhatExclude(),
            'sort_by' => 'date',
            'distance' => $apiSettings->getDistance(),
            'max_days_old' =>   $maxOldDays  // Exclusion de certains mots-clés
        ];

        $franceTravailParams = [
            'motsCles' => $apiSettings->getWhat(),      // Mot clé de recherche
            'commune' => $apiSettings->getCity()->getInseeCode(),     // Localisation
            'what_exclude' => $apiSettings->getWhatExclude(),
            'distance' => $apiSettings->getDistance(),
            'publieeDepuis' =>   4  // Exclusion de certains mots-clés
        ];

        // Calcul du nombre de secondes jusqu'à minuit
        $now = new DateTime();
        $midnight = new DateTime('tomorrow midnight');
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

        $noInfoStr = 'Non renseigné';
        // Récupérer la réponse des données en cache
        $jobResponseData = $cachedData['response'];
        $adzunaJobResults = array_map(
            function ($job) use ($noInfoStr) {
                $created = new DateTime($job['created']);
                return [
                    'source' => 'Adzuna',
                    'company' => $job['company']['display_name'] ?? $noInfoStr,
                    'location' => explode(',', $job['location']['display_name'])[0] ?? $noInfoStr,
                    'description' => $job['description'] ?? $noInfoStr,
                    'title' => $job['title'] ??  $noInfoStr,
                    'link' => $job['redirect_url'] ??  '',
                    'created' => $created->format('d/m/y') ??  $noInfoStr,
                    'id' => $job['id']
                ];
            },
            $jobResponseData['azduna']['results']
        );
        $franceTravailJobResults = array_map(function ($job) {
            $created = new DateTime($job['dateCreation']);
            $company = 'non renseigné';

            if (isset($job['entreprise']['nom'])) {
                $company = $job['entreprise']['nom'];
            }

            $link = 'https://candidat.francetravail.fr/offres/recherche/detail/' . $job['id'];
            if (isset($job['contact']['urlPostulation'])) {
                $link = $job['contact']['urlPostulation'];
            }

            return [
                'source' => 'France travail',
                'company' =>   $company,
                'location' => substr($job['lieuTravail']['libelle'], 4),
                'type_contrat' => $job['typeContratLibelle'],
                'description' => $job['description'],
                'title' => $job['intitule'],
                'link' => $link,
                'created' => $created->format('d/m/y'),
                'id' => $job['id']
            ];
        }, $jobResponseData['franceTravail']['resultats']);

        // Rendu du template avec les résultats
        return $this->render('job/job_alert.html.twig', [
            'adzunaJobResults' => $adzunaJobResults,
            'franceTravailJobResults' => $franceTravailJobResults,

        ]);
    }
}
