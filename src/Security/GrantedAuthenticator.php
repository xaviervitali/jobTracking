<?php

namespace App\Security;

use App\Entity\User;
use App\Service\FailedLoginAttemptService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class GrantedAuthenticator extends AbstractAuthenticator
{

    private UserProviderInterface $userProvider;
    private FailedLoginAttemptService $failedLoginAttemptService;
    private User $user;

    public function __construct(UserProviderInterface $userProvider, FailedLoginAttemptService $failedLoginAttemptService)
    {
        $this->userProvider = $userProvider;
        $this->failedLoginAttemptService = $failedLoginAttemptService;
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get("_route") === "app_login" && $request->isMethod('POST');
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('_username');
        $password = $request->request->get('_password');
        $ipAddress = $request->getClientIp();
        if (!$email || !$password) {
            throw new CustomUserMessageAuthenticationException('Email et mot de passe requis.');
        }

        if ($this->failedLoginAttemptService->isBlocked($ipAddress, $email)) {
            throw new CustomUserMessageAuthenticationException($this->failedLoginAttemptService->getBlockedMessage());
        }

        // Utilisation de loadUserByIdentifier (Symfony >= 5.3)
        $this->user = $this->userProvider->loadUserByIdentifier($email);
        if ($this->user && !$this->user->isVerified()) {
            throw new CustomUserMessageAuthenticationException('Votre compte n\'est pas encore vérifié.');
        }

        if (!$this->user || !$this->checkCredentials($password, $this->user->getPassword())) {
           $message =  $this->failedLoginAttemptService->recordFailedAttempt($ipAddress, $email);
            throw new CustomUserMessageAuthenticationException($message);
        }

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {

        $ipAddress = $request->getClientIp();
        $email = $request->request->get('_username');
        $this->failedLoginAttemptService->clearFailedAttempts($ipAddress, $email);

        return null; // null laisse Symfony continuer
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        // TODO: Implement onAuthenticationFailure() method.
        $session = $request->getSession();
        if ($session !== null) {
            // Vérifier si la session supporte FlashBagInterface
            if ($session->isStarted()) {
                $session->getFlashBag()->add('error', $exception->getMessage());
            }
        }

        // Rediriger vers la route de login
        return new RedirectResponse('/login');

    }
    private function checkCredentials(string $password, string $hashedPassword): bool
    {
        // Utilisez votre méthode de vérification de mot de passe ici
        // Par exemple, si vous utilisez bcrypt :
        return password_verify($password, $hashedPassword);
    }

    //    public function start(Request $request, AuthenticationException $authException = null): Response
    //    {
    //        /*
    //         * If you would like this class to control what happens when an anonymous user accesses a
    //         * protected page (e.g. redirect to /login), uncomment this method and make this class
    //         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
    //         *
    //         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
    //         */
    //    }
}
