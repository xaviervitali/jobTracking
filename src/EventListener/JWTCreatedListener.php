<?php

namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: Events::JWT_CREATED)]
final class JWTCreatedListener
{
    public function __invoke(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $payload = $event->getData();
        $payload['id'] = $user->getId();
        $payload['firstname'] = $user->getFirstname(); // si besoin
        $payload['lastname'] = $user->getLastname(); // si besoin
        $payload['email'] = $user->getEmail(); // si besoin

        $event->setData($payload);
    }
}