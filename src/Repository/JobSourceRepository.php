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


        $sql = "SELECT a.name, js.name as source, COUNT(*) as count
                FROM jt_job j
                INNER JOIN jt_job_source js ON js.id = j.source_id
                INNER JOIN jt_job_tracking jt ON jt.job_id = j.id
                INNER JOIN jt_action a ON a.id = jt.action_id
                WHERE j.user_id = :userId AND a.name != :notActionName
                GROUP BY a.name, js.name";

        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->executeQuery($sql, ['userId' => $user->getId(), 'notActionName' => ActionStatus::getStartActionName()]);

        return $stmt->fetchAllAssociative();
    }
}
