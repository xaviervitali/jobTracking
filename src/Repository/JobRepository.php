<?php

namespace App\Repository;

use App\Entity\Job;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Job>
 */
class JobRepository extends ServiceEntityRepository
{

    private $connection;


    public function __construct(ManagerRegistry $registry, Connection $connection)
    {
        parent::__construct($registry, Job::class);
        $this->connection = $connection;
    }




    public function isClosedJob(Job $job)
    {
        $sql = "select sum(action.set_closed) as sum from job_tracking INNER join action on action.id = job_tracking.action_id where job_tracking.job_id = :job;";
        $stmt = $this->connection->executeQuery($sql, params: ['job' => $job->getId()]);

        $queryArr = $stmt->fetchAllAssociative()[0];
        return !!$queryArr['sum'];
    }


    public function getAvgDelay(User $user)
    {
        $sql = "SELECT 
                    AVG(TIMESTAMPDIFF(DAY, t1.created_at, t2.created_at)) AS avg_delay_days
                FROM
                    job_tracking t1
                JOIN
                    job_tracking t2
                ON
                    t1.job_id = t2.job_id
                AND
                    t1.created_at < t2.created_at
                AND
                    NOT EXISTS (SELECT 1 
                                FROM job_tracking t3
                                WHERE t3.job_id = t1.job_id 
                                AND t3.created_at > t1.created_at 
                                AND t3.created_at < t2.created_at and t1.user_id = :user);";
        $stmt = $this->connection->executeQuery($sql, ['user' => $user->getId()]);
        return $stmt->fetchAssociative();
    }

}
