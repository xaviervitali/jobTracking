<?php

namespace App\Controller;

use App\Entity\Action;
use App\Entity\JobApiServices;
use App\Entity\JobSource;
use App\Form\AdminActionType;
use App\Form\JobApiServicesType;
use App\Form\JobSourceType;
use App\Repository\ActionRepository;
use App\Repository\JobApiServicesRepository;
use App\Repository\JobSourceRepository;
use App\Repository\UserRepository;
use App\Service\JobService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/admin', name: 'app_admin_')]
class AdminController extends AbstractController
{

    #[Route('/index', name: 'index')]
    public function index(Request $request, EntityManagerInterface $entityManager, JobSourceRepository $jobSourceRepository,  SerializerInterface $serializer,JobApiServicesRepository $jobApiServicesRepository, ActionRepository $actionRepository, UserRepository $userRepository): Response
    {
        $jobApi = new JobApiServices();
        $form = $this->createForm(JobApiServicesType::class, $jobApi);
        $form->handleRequest($request);

        $allJobApiServices = $jobApiServicesRepository->findAll();
        $allJobApiServicesJson = $serializer->serialize($allJobApiServices, 'json', ['groups' => ['api_service']] );

        $allUsers = $userRepository->findAll();
        $allUsersJson = $serializer->serialize($allUsers, 'json', ['groups' => ['user_show']] );

        $jobSources = $jobSourceRepository->findAll();
        foreach ($jobSources as $jobSource) {
            $jobSource->setJobCount($jobSource->getJobs()->count());
        }
        $allJobSourcesJson =  $serializer->serialize($jobSources, 'json', ['groups' => ['job_source']] );

        $actions = $actionRepository->findAll();

        foreach($actions as $action){
            $action->setJobTrackingsCount($action->getJobTrackings()->count());
        };

        $allActionsJson =  $serializer->serialize($actions, 'json', ['groups' => ['job']] );
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($jobApi);
            $entityManager->flush();
        }

        return $this->render('admin/index.html.twig', [
            'formApi' => $form,
            'allJobApiServicesJson'=>$allJobApiServicesJson,
            'allJobSourcesJson'=>$allJobSourcesJson,
            'allActionsJson'=>$allActionsJson,
            'allUsersJson'=>$allUsersJson,

        ]);

    }


    #[Route('/job_service/{id}', name: 'job_service_edit', methods: ['GET', 'POST'])]
    public function jobServiceEdit(Request $request, JobApiServices $jobApiServices, EntityManagerInterface $entityManager): Response
    {


        $form = $this->createForm(JobApiServicesType::class, $jobApiServices);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/job_service/edit.html.twig', [
            'job_service' => $jobApiServices,
            'formApi' => $form,
        ]);
    }

    #[Route('/job_service/', name: 'job_service_new', methods: ['GET', 'POST'])]
    public function jobServiceNew(Request $request,  EntityManagerInterface $entityManager): Response
    {
        $jobApiServices = new JobApiServices();

        $form = $this->createForm(JobApiServicesType::class, $jobApiServices);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($jobApiServices);

            $entityManager->flush();
            return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/job_service/new.html.twig', [
            'formApi' => $form,
        ]);
    }

    #[Route('/job_service/{id}/delete', name: 'job_service_delete', methods: ['GET', 'POST'])]
    public function jobServiceDelete(Request $request, JobApiServices $jobApiServices, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $jobApiServices->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($jobApiServices);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/job_source/{id}', name: 'job_source_edit', methods: ['GET', 'POST'])]
    public function jobSourceEdit(Request $request, JobSource $jobSource, EntityManagerInterface $entityManager): Response
    {


        $form = $this->createForm(JobSourceType::class, $jobSource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/job_source/edit.html.twig', [
            'job_source' => $jobSource,
            'formApi' => $form,
        ]);
    }

    #[Route('/job_source/', name: 'job_source_new', methods: ['GET', 'POST'])]
    public function jobSourceNew(Request $request,  EntityManagerInterface $entityManager): Response
    {
        $jobSource = new JobSource();

        $form = $this->createForm(JobSourceType::class, $jobSource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($jobSource);

            $entityManager->flush();

            return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/job_source/new.html.twig', [
            'formApi' => $form,
        ]);
    }

    #[Route('/job_source/{id}/delete', name: 'job_source_delete', methods: ['GET', 'POST'])]
    public function jobSourceDelete(Request $request, JobSource $jobSource,  EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $jobSource->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($jobSource);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
    }



    #[Route('/action/{id}', name: 'action_edit', methods: ['GET', 'POST'])]
    public function actionEdit(Request $request,Action $action, EntityManagerInterface $entityManager): Response
    {


        $form = $this->createForm(AdminActionType::class, $action);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->flush();

            return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/action/edit.html.twig', [
            'action' => $action,
            'formApi' => $form,
        ]);
    }

    #[Route('/action/', name: 'action_new', methods: ['GET', 'POST'])]
    public function actionNew(Request $request,  EntityManagerInterface $entityManager): Response
    {
        $action = new Action();

        $form = $this->createForm(AdminActionType::class, $action);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($action);

            $entityManager->flush();

            return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/action/new.html.twig', [
            'formApi' => $form,
        ]);
    }

    #[Route('/action/{id}/delete', name: 'action_delete', methods: ['GET', 'POST'])]
    public function actionDelete(Request $request, Action $action,  EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $action->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($action);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
