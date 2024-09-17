<?php

namespace App\Repository;

use App\Entity\JobTracking;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Response>
 */
class JobTrackingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, JobTracking::class);
    }

    /**
     * Récupère les enregistrements de JobTracking avec les conditions spécifiées.
     *
     * @return JobTracking[]
     */
    public function findJobsByUserOrderedByDate($user)
    {
        //         $sql = 'SELECT job.created_at as job_created_at, job_tracking.created_at as job_tracking_created_at , action.name , job.title, job.recruiter, job.id, SUM(action.set_closed) AS actionValues, Max(job_tracking.id)
        // FROM `job_tracking`
        // LEFT JOIN job ON job.id = job_tracking.job_id
        // INNER JOIN user ON user.id = job.user_id
        // INNER JOIN action ON job_tracking.action_id = action.id
        // WHERE user.id = :userId
        // GROUP BY job.id
        // HAVING actionValues = 0
        // ORDER BY job_tracking.created_at, job.created_at;';

        $sql = "SELECT job.created_at as job_created_at, job_tracking.created_at as job_tracking_created_at , action.name , job.title, job.recruiter, job.id, MAX(job_tracking.created_at), SUM(action.set_closed)  AS isClosed FROM `job_tracking` left join job on job.id = job_tracking.job_id inner join action on job_tracking.action_id = action.id WHERE job.user_id= :userId group by job.id 
        HAVING isClosed = 0 
        ORDER BY job_tracking.created_at, job.created_at;";
        $conn = $this->getEntityManager()->getConnection();
        $stmt = $conn->executeQuery($sql, ['userId' => $user->getId()]);

        return $stmt->fetchAllAssociative();
    }

    //    /**
    //     * @return Response[] Returns an array of Response objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Response
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
