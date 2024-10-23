<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Service\ApiService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class ApiController extends AbstractController
{

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
            'max_days_old' => $maxOldDays
        ];

        $franceTravailParams = [
            'motsCles' => $apiSettings->getWhat(),      // Mot clé de recherche
            'commune' => $apiSettings->getCity()->getInseeCode(),     // Localisation
            'what_exclude' => $apiSettings->getWhatExclude(),
            'distance' => $apiSettings->getDistance(),
            'publieeDepuis' => $maxOldDays
        ];

        $joobleParams = [
            'keywords' => $apiSettings->getWhat(),
            'location' => $apiSettings->getCity()->getZipCode(),
            'radius' => $apiSettings->getDistance(),
            "companysearch" => "false",
            "page"=> "1",
        ];

        // Calcul du nombre de secondes jusqu'à minuit
        $now = new DateTime();
        $midnight = new DateTime('tomorrow midnight');
        $secondsUntilMidnight = $midnight->getTimestamp() - $now->getTimestamp();

        // Générer une clé de cache unique pour chaque utilisateur
        $cacheKey = 'adzuna_api_' . $user->getId();

        // Récupération des données en cache ou appel à l'API si nécessaire
        $cachedData = $cache->get($cacheKey, function (ItemInterface $item) use ($secondsUntilMidnight, $apiService, $adzunaParams, $franceTravailParams, $apiSettings, $joobleParams, $params) {
            $item->expiresAfter($secondsUntilMidnight); // Expiration à minuit

            // Appel à l'API via ApiService et retourne les données avec les paramètres actuels
            $adzunaJobResponseData = $apiService->getAdzunaJobs($adzunaParams, $apiSettings->getCountry());

            $franceTravailJobResponseData = $apiService->getFranceTravailJobs($franceTravailParams);

            $joobleJobs = $apiService->getJoobleJobs($joobleParams);
            // Retourner les paramètres et la réponse de l'API
            return [
                'params' => $params,
                'response' => ['azduna' => $adzunaJobResponseData, 'franceTravail' => $franceTravailJobResponseData, 'jooble'=>$joobleJobs],
            ];
        });

        // Comparer les paramètres actuels avec ceux en cache
        if (isset($cachedData['params']) && $cachedData['params'] !== $params) {
            // Si les paramètres ont changé, faire une nouvelle requête API
            $cachedData = [
                'params' => $params,
                'response' => ['azduna' => $apiService->getAdzunaJobs($adzunaParams, $apiSettings->getCountry()), 'franceTravail' => $apiService->getFranceTravailJobs($franceTravailParams), 'jooble'=> $apiService->getJoobleJobs($joobleParams)],
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
                    'title' => $job['title'] ?? $noInfoStr,
                    'link' => $job['redirect_url'] ?? '',
                    'created' => $created->format('d/m/y') ?? $noInfoStr,
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
                'company' => $company,
                'location' => substr($job['lieuTravail']['libelle'], 4),
                'type_contrat' => $job['typeContratLibelle'],
                'description' => $job['description'],
                'title' => $job['intitule'],
                'link' => $link,
                'created' => $created->format('d/m/y'),
                'id' => $job['id']
            ];
        }, $jobResponseData['franceTravail']['resultats']);


        $joobleResults = array_map(function ($job) {
            $created = new DateTime($job['updated']);

            return [
                'source' => 'Jooble',
                'company' => $job['company'],
                'description' => $job['snippet'],
                'title' => $job['title'],
                'location' =>  $job['location'],
                'link' =>  $job['link'],
                'type_contrat' => $job['type'],
                'created' => $created->format('d/m/y')
            ];
        }, $jobResponseData['jooble']['jobs']);

        // Rendu du template avec les résultats
        return $this->render('job/job_alert.html.twig', [
            'adzunaJobResults' => $adzunaJobResults,
            'franceTravailJobResults' => $franceTravailJobResults,
            'joobleResults' => $joobleResults,

        ]);
    }
}
