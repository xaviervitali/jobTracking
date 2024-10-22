<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\CV;
use App\Entity\JobSearchSettings;
use App\Entity\User;
use App\Form\JobSearchSettingsType;
use App\Form\CvType;
use App\Repository\CityRepository;
use App\Repository\JobRepository;
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


        $jobsInProgressByUser = $jobRepository->findJobsInProgressOrClosedByUser($user);
        $jobsCountPerDelay = $jobRepository->getJobsCountPerDelay($user);

        return $this->render('home/index.html.twig', [
            'jobsInProgressJson' => json_encode($jobsInProgressByUser),
            'jobsInProgress' => $jobsInProgressByUser,
            'jobsCountPerDelay' => $jobsCountPerDelay,
        ]);
    }


    #[Route('/mon_espace', name: 'app_user_show')]
    public function show(JobRepository $jobRepository, SerializerInterface $serializer, Security $security, EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $security->getUser()->getUserIdentifier()]);

        $userJobs = $jobRepository->findByUser($user);
        // $jsonContent = $serializer->serialize($userJobs, 'json', [
        //     'groups' => ['job'],

        // ]);

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
            $apiSettings
                ->setCity($city)
                ->setCountry('fr');
            $entityManager->flush();
            return $this->redirectToRoute('app_job_alert');
        }

        return $this->render('home/my-space.html.twig', [
            'jobs' => $userJobs,
            'user' => $user,
            'formCV' => $formCV,
            'formApiSettings' => $formApiSettings,

        ]);
    }
}
