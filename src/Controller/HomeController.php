<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\CV;
use App\Entity\JobApiServices;
use App\Entity\JobSearchSettings;
use App\Entity\User;
use App\Form\JobSearchSettingsType;
use App\Form\CvType;
use App\Repository\AddressBookRepository;
use App\Repository\CityRepository;
use App\Repository\JobRepository;
use App\Service\JobService;
use DateTime;
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
        JobRepository $jobRepository,
        Security $security,
    ): Response {
        $user = $security->getUser();
        $date = new DateTime();
        $date->modify('-1 year');
        $date = DateTimeImmutable::createFromMutable($date);


        $jobsInProgressByUser = $jobRepository->findJobsInProgressByUser($user);

        return $this->render('home/index.html.twig', [
            'jobsInProgressJson' => json_encode($jobsInProgressByUser),
            'jobsInProgress' => $jobsInProgressByUser,
        ]);
    }


    #[Route('/mon_espace', name: 'app_user_show')]
    public function show(JobRepository $jobRepository, SerializerInterface $serializer, Security $security, EntityManagerInterface $entityManager, Request $request, AddressBookRepository $addressBookRepository,): Response
    {
        

        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $security->getUser()->getUserIdentifier()]);


        $jobService = new JobService($user, new DateTimeImmutable(), $jobRepository);

        $userJobs =   $jobService->getJobsByUser();

        $cv = new CV();
        $formCV = $this->createForm(CvType::class, $cv, [
            'action' => $this->generateUrl('cv_new'), // Remplace 'nom_de_la_route' par ta route Symfony
        ]);

        $apiSettings = $entityManager->getRepository(JobSearchSettings::class)->findOneBy(['user' => $user]);

        if (!$apiSettings) {
            $apiSettings = new JobSearchSettings();
            $apiSettings->setUser($user);  // Lier les paramètres API à l'utilisateur
            // Persister la nouvelle entité
            $entityManager->persist($apiSettings);
        }

        // Créer le formulaire AdzunaApiSettings
        $formApiSettings = $this->createForm(JobSearchSettingsType::class, $apiSettings);



        $formApiSettings->handleRequest($request);

        if ($formApiSettings->isSubmitted()) {
            $cityId =   $formApiSettings->get('city')->getData();
            $city = $entityManager->getRepository(City::class)->findOneBy(['id' => $cityId]);

            $jobApis = [...$formApiSettings->get('jobApiServices')->getData()];
            
            $allApiServices = $entityManager->getRepository(JobApiServices::class)->findAll();

            foreach( $allApiServices as  $jobApi){
                $apiSettings->removeJobApiService($jobApi);
            }

            foreach ($jobApis as $jobApi) {
                $apiSettings->addJobApiService($jobApi);
            }

            $apiSettings
                ->setCity($city)
                ->setCountry('fr')
            ;


            $entityManager->flush();
            $this->addFlash('success', 'Les paramètres de recherche d\'emploi ont été mis à jour avec succès.');
            // return $this->redirectToRoute('app_job_alert');
        }

        $addressBooks = $addressBookRepository->findBy(['user' => $security->getUser()]);
        $addressBooksJson = $serializer->serialize($addressBooks, 'json', ['groups' => ['address_book']]);

        return $this->render('home/my-space.html.twig', [
            'jobs' => $userJobs,
            'user' => $user,
            'formCV' => $formCV,
            'formApiSettings' => $formApiSettings,
            'address_books_json' => $addressBooksJson,
            'contacts' => count($addressBooks)


        ]);
    }
    #[Route('/mentions_legales', name: 'app_rgpd')]
    function rgpd()
    {
        return $this->render('rgpd.html.twig', []);
    }

    #[Route('/about', name: 'app_about')]
    function about()
    {
        return $this->render('about.html.twig', []);
    }
}
