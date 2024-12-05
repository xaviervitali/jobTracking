<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\FailedLoginAttempt;
use DateTime;
use DateTimeImmutable;
use Twig\Environment;

class FailedLoginAttemptService
{

    private $blockedMessage = "Votre compte est bloqué pour des raisons de sécurité. Merci de réessayer plus tard.";
    public function __construct(private EntityManagerInterface $entityManager,private Environment $twig, private EmailService $emailService)
    {
    }

    public function recordFailedAttempt($ipAddress, $username): string
    {
        $failedAttempt = $this->entityManager->getRepository(FailedLoginAttempt::class)
            ->findOneBy(['ipAddress' => $ipAddress, 'username' => $username]);
        $attemps = $failedAttempt?->getAttempts() ?? 0;
        $message = $attemps >=  4 ? $this->blockedMessage :  "Identifiants incorrects plus que " . (4 - $attemps) .  " essais" ;

        if (!$failedAttempt) {
            $failedAttempt = new FailedLoginAttempt();
            $failedAttempt->setIpAddress($ipAddress);
            $failedAttempt->setUsername($username);
            $failedAttempt->setAttempts(1);
            $failedAttempt->setLastAttemptAt(new DateTimeImmutable());
        } else {
            $failedAttempt->setAttempts( $attemps + 1);
            $failedAttempt->setLastAttemptAt(new DateTimeImmutable());
        }

        if ( $attemps >= 4) {
            $now = new DateTime('+5 minutes');
            $failedAttempt->setBlockedUntil(DateTimeImmutable::createFromMutable($now));
            $message = $this->blockedMessage;
            $htmlContent = $this->twig->render('security/account_blocked.html.twig', [
                'ip'=> $ipAddress
            ]);
            
            $this-> emailService->sendHtmlEmail($username, 'Tentative de piratage', $htmlContent);
        }


        $this->entityManager->persist($failedAttempt);
        $this->entityManager->flush();
        return $message;
    }

    public function isBlocked($ipAddress, $username)
    {
        $failedAttempt = $this->entityManager->getRepository(FailedLoginAttempt::class)
            ->findOneBy(['ipAddress' => $ipAddress, 'username' => $username]);

        if ($failedAttempt && $failedAttempt->getBlockedUntil() > new DateTime()) {
            return true;
        }

        return false;
    }

    public function clearFailedAttempts($ipAddress, $username)
    {
        $failedAttempt = $this->entityManager->getRepository(FailedLoginAttempt::class)
            ->findOneBy(['ipAddress' => $ipAddress, 'username' => $username]);

        if ($failedAttempt) {
            $this->entityManager->remove($failedAttempt);
            $this->entityManager->flush();
        }
    }

    public function getBlockedMessage(){
        return $this->blockedMessage;
    }
}
