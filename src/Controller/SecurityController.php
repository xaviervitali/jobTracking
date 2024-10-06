<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotPasswordType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Security $security): Response
    {
        if(!!$security->getUser()){
            return $this->redirectToRoute('app_synthese');
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, EmailService $emailService): Response
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            
            $email = $form->get('email')->getData();
            $user = $userRepository->findOneBy(['email' => $email]);
            if ($user) {
                $token = bin2hex(random_bytes(32)); // Générer un token
                $user->setToken($token); // Ajoutez un champ `resetToken` à votre entité User
                $entityManager->persist($user);
                $entityManager->flush();
    
                // Envoyez un e-mail à l'utilisateur
                $resetLink = $this->generateUrl('app_reset_password', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                $htmlContent = $this->renderView('security/reset_password_email.html.twig', [
                    'resetLink' => $resetLink,
                ]);
                dump($htmlContent);
                $emailService->sendHtmlEmail($user->getEmail(), 'Réinitialisation de votre mot de passe', $htmlContent);

            } 
            $this->addFlash('success', "Un e-mail à l\'adresse $email de réinitialisation a été envoyé.");
    
            return $this->redirectToRoute('app_login');
        }
    
        return $this->render('security/forgot_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function reset(Request $request, $token, EntityManagerInterface $entityManager, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher,): Response
    {
        // Recherchez l'utilisateur avec le token
        $user = $userRepository->findOneBy(["token" => $token]); // Recherchez l'utilisateur par le token ici

        if (!$user) {
            throw new AccessDeniedException('Token invalide.');
        }

        $form = $this->createForm(ResetPasswordType::class); // Créez ce formulaire pour le nouveau mot de passe
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            );

            $user->setPassword($hashedPassword);
            $user->setResetToken(null); // Effacez le token
            $entityManager->persist($user);
            $entityManager->flush();

            // Ajoutez un message flash pour l'utilisateur
            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password_form.html.twig', [
            'form' => $form->createView(),

        ]);
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
