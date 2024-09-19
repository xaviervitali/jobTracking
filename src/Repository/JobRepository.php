<?php

namespace App\Repository;

use App\Entity\Job;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Job>
 */
class JobRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Job::class);
    }


    

   public function findByUserAndMoreThanDate(User $user, DateTimeImmutable $date): ?array
   {
       return $this->createQueryBuilder('j')
           ->andWhere('j.user = :user')
           ->setParameter('user', $user)
           ->andWhere('j.createdAt > :date')
           ->setParameter('date', $date)
           ->getQuery()
           ->getResult()
       ;
   }
}
