<?php

namespace App\EventListener;

use App\Entity\AddressBook;
use App\Entity\CV;
use App\Entity\Job;
use App\Entity\JobSearchSettings;
use App\Entity\JobTracking;
use App\Entity\Note;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

#[AsDoctrineListener(event: Events::prePersist)]
final class SetUserAndDateListener
{
    public function __construct(private Security $security) {}

    public function prePersist(PrePersistEventArgs $args): void
    {
        $entity = $args->getObject();
        $user   = $this->security->getUser();

        // Set createdAt if the entity has it and it's not set
        if (method_exists($entity, 'getCreatedAt') && method_exists($entity, 'setCreatedAt') && $entity->getCreatedAt() === null) {
            $entity->setCreatedAt(new \DateTimeImmutable());
        }

        // Set user if the entity belongs to a user
        if ($user !== null && method_exists($entity, 'getUser') && method_exists($entity, 'setUser') && $entity->getUser() === null) {
            if ($entity instanceof Job || $entity instanceof JobTracking || $entity instanceof Note || $entity instanceof AddressBook || $entity instanceof CV || $entity instanceof JobSearchSettings) {
                $entity->setUser($user);
            }
        }
    }
}
