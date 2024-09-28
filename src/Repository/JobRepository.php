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


        $sql = "SELECT j.id, j.title, j.created_at, j.recruiter, a.name, a.set_closed, MAX(jt.created_at) AS maxCreatedAt FROM job j inner JOIN job_tracking jt ON jt.job_id = j.id inner JOIN action a ON a.id = jt.action_id WHERE j.user_id = :user AND ( jt.created_at = ( SELECT MAX(jt2.created_at) FROM job_tracking jt2 WHERE jt2.job_id = j.id ) ) AND ( (:inProgress = 1 AND (jt.id IS NULL OR a.set_closed = 0 OR a.set_closed IS NULL)) OR (:inProgress = 0 AND (jt.id IS NOT NULL AND a.set_closed = 1)) ) GROUP BY j.id, a.name, a.set_closed, j.title, j.created_at, j.recruiter ORDER BY maxCreatedAt DESC;";

        $where = $inProgress ? 'jt IS NULL OR a.setClosed = 0 OR a.setClosed is null' : 'jt IS NOT NULL AND a.setClosed = 1';


        $stmt = $this->connection->executeQuery($sql, ['user' => $user->getId(), 'inProgress' => $inProgress]);

        return $stmt->fetchAllAssociative();
        // return  $this->createQueryBuilder('j')
        //     ->innerJoin('j.jobTracking', 'jt') // Jointure avec job_tracking
        //     ->innerJoin('jt.action', 'a')      // Jointure avec action
        //     ->where('j.user = :user')         // Condition sur l'utilisateur
        //     ->setParameter('user', $user)
        //     ->groupBy('j.id')                 // Grouper par job.id
        //     ->addSelect('MAX(jt.createdAt) as maxCreatedAt') // Sélectionner la date max
        //     ->addSelect('a.name') // Sélectionner la date max
        //     ->addSelect('a.setClosed') // Sélectionner la date max
        //     ->addSelect('j.title') // Sélectionner la date max
        //     ->addSelect('j.createdAt') // Sélectionner la date max
        //     ->addSelect('j.id') // Sélectionner la date max
        //     ->addSelect('j.recruiter')
        //     ->andWhere($where)

        //     ->orderBy('maxCreatedAt', 'desc') // Tri par la date max
        //     ->getQuery()
        //     ->getResult();
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
