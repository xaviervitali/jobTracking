<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/user')]
final class UserController extends AbstractController
{
    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,  UserPasswordHasherInterface $passwordHasher, Security $security, EmailService $emailService): Response
    {
        if (!!$security->getUser()) {
            return $this->redirectToRoute('app_synthese');
        }
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );

            $user->setPassword($hashedPassword);

            $token = bin2hex(random_bytes(32));  // Générer un token de 64 caractères
            $user->setToken($token);

            // Persist et flush dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Envoi de l'e-mail de validation
            $validationLink = $this->generateUrl('app_user_confirm', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
            $emailService->sendHtmlEmail(
                $user->getEmail(),
                'Validation de votre inscription',
                $this->renderView('user/confirm.html.twig', [
                    'user' => $user,
                    'validationLink' => $validationLink,
                ])
            );


            // return $this->redirectToRoute('app_synthese', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }


    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/confirm/{token}', name: 'app_user_confirm')]
    public function confirmEmail(string $token, EntityManagerInterface $entityManager): Response
    {
        // Rechercher l'utilisateur par token
        $user = $entityManager->getRepository(User::class)->findOneBy(['token' => $token]);

        if (!$user) {
            // Si aucun utilisateur trouvé avec ce token
            throw $this->createNotFoundException('Ce lien de validation est invalide.');
        }

        // Valider l'utilisateur (par exemple, activer un champ isVerified ou autre)
        $user->setIsVerified(true);
        $user->setToken(null);  // Supprimer le token après la validation

        // Sauvegarder les changements
        $entityManager->flush();

        // Rediriger vers une page de confirmation
        return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
    }
}
