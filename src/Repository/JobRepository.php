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




    public function findByUser(User $user): ?array
    {
        return $this->createQueryBuilder('j')
            ->andWhere('j.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult()
        ;
    }
    public function findJobsInProgressByUser(User $user): ?array
    {

        return $this->createQueryBuilder('j')
            ->leftJoin('j.jobTracking', 'jt') // Jointure avec job_tracking
            ->leftJoin('jt.action', 'a')      // Jointure avec action
            ->where('j.user = :user')         // Condition sur l'utilisateur
            ->setParameter('user', $user)
            ->groupBy('j.id')                 // Grouper par job.id
            ->addSelect('MAX(jt.createdAt) as maxCreatedAt') // Sélectionner la date max
            ->addSelect('a.name') // Sélectionner la date max
            ->addSelect('a.setClosed') // Sélectionner la date max
            ->addSelect('j.title') // Sélectionner la date max
            ->addSelect('j.createdAt') // Sélectionner la date max
            ->addSelect('j.id') // Sélectionner la date max
            ->addSelect('j.recruiter') // Sélectionner la date max
            ->andWhere('jt IS NULL OR a.setClosed = 0 OR a.setClosed is null')
            ->orderBy('maxCreatedAt', 'desc') // Tri par la date max
            ->getQuery()
            ->getResult();
    }
}
