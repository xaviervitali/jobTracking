<?php

namespace App\Controller;

use App\Entity\CV;
use App\Form\CvType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CVController extends AbstractController
{

    #[Route('/cv/new', name: 'cv_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security)
    {
        $cv = new CV();
        $formCV = $this->createForm(CvType::class, $cv);
        $formCV->handleRequest($request);
        $user = $security->getUser();

        if ($formCV->isSubmitted() && $formCV->isValid()) {
            $currentCV = new CV();
            $cvFile = $formCV->get('cvFile')->getData();
            if ($cvFile) {
                $originalFilename = pathinfo($cvFile->getClientOriginalName(), PATHINFO_FILENAME);
                $currentCV->setCVFile($cvFile);
                $currentCV
                    ->setTitle($originalFilename)
                    ->setUser($user);
                $entityManager->persist($currentCV);
                $entityManager->flush();

            }

        }
       return $this->redirectToRoute('app_user_show', ["id" => $user]);

    }

    #[Route('/cv/{id}', name: 'cv_show')]
    public function show(CV $cv)
    {
        return $this->render('cv/show.html.twig', [
            'cv' => $cv,
        ]);
    }
    #[Route('/cv/{id}/delete', name: 'cv_delete', methods: ['POST'])]
    public function delete(Request $request, CV $cv, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $cv->getId(), $request->request->get('_token'))) {
            $entityManager->remove($cv);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_show', [], Response::HTTP_SEE_OTHER);
    }

 

}