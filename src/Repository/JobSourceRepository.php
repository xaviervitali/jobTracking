<?php

namespace App\Repository;

use App\Entity\JobSource;
use App\Entity\User;
use App\Enums\ActionStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<JobSource>
 */
class JobSourceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobSource::class);
    }

    //    /**
    //     * @return JobSource[] Returns an array of JobSource objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('j')
    //            ->andWhere('j.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('j.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?JobSource
    //    {
    //        return $this->createQueryBuilder('j')
    //            ->andWhere('j.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function getActionsNameAndCountByJobSource(User $user)
    {


        $sql = "SELECT  action.name, job_source.name as source, COUNT(*) as count FROM `job` inner join job_source on job_source.id = job.source_id inner join job_tracking on job_tracking.job_id = job.id INNER join action on action.id = job_tracking.action_id WHERE job.user_id = :userId and not action.name = :notActionName group by action.name, job_source.name";

        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->executeQuery($sql, ['userId' => $user->getId(), 'notActionName' => ActionStatus::getStartActionName()]);

        return $stmt->fetchAllAssociative();
    }
}
