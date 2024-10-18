<?php

namespace App\Controller;

use App\Repository\CVRepository;
use App\Repository\UserRepository;
use App\Service\ApiService;
use App\Service\MistralAiService;
use Smalot\PdfParser\Parser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MistralAiController extends AbstractController
{
    #[Route('/generateCoverLetter', name: 'app_generate_cover_letter')]
    public function index(Security $security, CVRepository $cVRepository, Request $request, ApiService $apiService, ParameterBagInterface $parameterBag): Response
    {
        $data = json_decode($request->getContent(), true);
        $jobDescription = $data['jobDescription'] ?? null;
        $cvId = $data['cv'] ?? null;
        $user = $security->getUser();
        $cv = $cVRepository->find(['id' => $cvId]);

        if ($cv->getUser() !== $user) {
            throw $this->createAccessDeniedException('Ce CV ne vous appartient pas');
        }

        $file = $cv->getCVName();
        $publicDir = $parameterBag->get('kernel.project_dir') . '/public';
        $cvFilePath = $publicDir . '/uploads/cv/' . $file;
        if ( !file_exists($cvFilePath) || empty($jobDescription) || empty($cvId)) {
            return $this->json('Les paramètres jobDescription et cvFilePath sont requis.', 400);
        }   

        try {
            $coverLetter = $apiService->generateCoverLetter($jobDescription, $cvFilePath);
            return $this->json(['coverLetter' => $coverLetter]);
        } catch (\Exception $e) {
            return $this->json('Erreur lors de la génération de la lettre de motivation : ' . $e->getMessage(), 500);
        }
    }
}
