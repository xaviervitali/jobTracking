<?php

namespace App\Repository;

use App\Entity\Job;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Job>
 */
class JobRepository extends ServiceEntityRepository

{

    private $entityManager;
    private $connection;
    

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager, Connection $connection)
    {
        parent::__construct($registry, Job::class);
        $this->entityManager = $entityManager;
        $this->connection = $connection;
    }




    public function findByUser(User $user): ?array
    {

        return  $this->createQueryBuilder('j')
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
            ->addSelect('j.recruiter')
            ->orderBy('maxCreatedAt', 'desc') // Tri par la date max
            ->getQuery()
            ->getResult();
    }

    public function findJobsInProgressOrClosedByUser(User $user, $inProgress = true): ?array
    {

        $where = $inProgress ? 'jt IS NULL OR a.setClosed = 0 OR a.setClosed is null' : 'jt IS NOT NULL AND a.setClosed = 1';

        return  $this->createQueryBuilder('j')
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
            ->addSelect('j.recruiter')
            ->andWhere($where)


            ->orderBy('maxCreatedAt', 'desc') // Tri par la date max
            ->getQuery()
            ->getResult();
    }

    public function findJobsUser(User $user): ?array
    {

        return $this->createQueryBuilder('j')
            ->leftJoin('j.jobTracking', 'jt') // Jointure avec job_tracking
            ->leftJoin('jt.action', 'a')      // Jointure avec action
            ->where('j.user = :user')         // Condition sur l'utilisateur
            ->setParameter('user', $user)
            ->groupBy('j.id')                 // Grouper par job.id
            ->addSelect('a.name') // Sélectionner la date max
            ->addSelect('a.setClosed') // Sélectionner la date max
            ->addSelect('j.title') // Sélectionner la date max
            ->addSelect('j.createdAt') // Sélectionner la date max
            ->addSelect('j.id') // Sélectionner la date max
            ->addSelect('j.recruiter') // Sélectionner la date max
            ->getQuery()
            ->getResult();
    }

    public function getJobsPerMonth($user)
    {

        $sql = "SELECT    DATE_FORMAT(created_at, '%Y-%m') AS yearmonth,
    COUNT(*) AS count  FROM `job` WHERE user_id = :user GROUP BY
    yearmonth
ORDER BY
    yearmonth;";


        $stmt = $this->connection->executeQuery($sql, ['user' => $user->getId()]);

        return $stmt->fetchAllAssociative();
    }

    public function getClosedJobsPerMonth($user)
    {

        $sql = "SELECT  count(*) as count, DATE_FORMAT(job_tracking.created_at, '%Y-%m') AS yearmonth, SUM(action.set_closed) as set_closed  FROM `job` INNER join job_tracking on job_tracking.job_id = job.id inner join action on job_tracking.action_id = action.id WHERE job.user_id = :user GROUP BY yearmonth HAVING set_closed > 0 ORDER BY yearmonth;";

        $stmt = $this->connection->executeQuery($sql, ['user' => $user->getId()]);

        return $stmt->fetchAllAssociative();
    }
}
