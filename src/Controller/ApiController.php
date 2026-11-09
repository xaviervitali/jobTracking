<?php

namespace App\Controller;

use App\Repository\CVRepository;
use App\Repository\UserRepository;
use App\Service\ApiService;
use App\Service\MistralAiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Cache\CacheInterface;

#[Route('/api')]
final class ApiController extends AbstractController
{
    #[Route('/job_alert', name: 'api_job_alert', methods: ['GET'])]
    public function jobAlert(Security $security, UserRepository $userRepository, CacheInterface $cache): JsonResponse
    {
        $user = $userRepository->findOneBy(['email' => $security->getUser()->getUserIdentifier()]);
        $userJobApiSettings = $user->getJobSearchSettings();

        if (!$userJobApiSettings || !count($userJobApiSettings->getJobApiServices())) {
            return $this->json(['error' => 'Aucun profil de recherche configuré.'], 400);
        }

        $apiService = new ApiService($userJobApiSettings, $cache);
        $results = $apiService->getUserApiJobResults();

        return $this->json(['jobs' => $results]);
    }

    #[Route('/generate_cover_letter', name: 'api_generate_cover_letter', methods: ['POST'])]
    public function generateCoverLetter(Security $security, CVRepository $cvRepository, Request $request, ParameterBagInterface $params, MistralAiService $mistralAiService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $jobDescription = $data['jobDescription'] ?? null;
        $cvId = $data['cv'] ?? null;

        if (!$cvId || !$jobDescription) {
            return $this->json(['error' => 'Les paramètres jobDescription et cv sont requis.'], 400);
        }

        $cv = $cvRepository->find($cvId);
        if (!$cv || $cv->getUser() !== $security->getUser()) {
            return $this->json(['error' => 'CV introuvable ou accès refusé.'], 403);
        }

        $cvFilePath = $params->get('kernel.project_dir') . '/public/uploads/cv/' . $cv->getCVName();
        if (!file_exists($cvFilePath)) {
            return $this->json(['error' => 'Fichier CV introuvable.'], 404);
        }

        try {
            $coverLetter = $mistralAiService->generateCoverLetter($jobDescription, $cvFilePath);
            return $this->json(['coverLetter' => $coverLetter]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
}
