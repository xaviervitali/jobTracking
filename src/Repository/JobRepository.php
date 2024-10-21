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




    public function findByUser(User $user): ?array
    {

        $sql = "SELECT j.id, j.title, j.created_at, j.recruiter, a.name, a.set_closed, MAX(jt.created_at) AS maxCreatedAt FROM job j inner JOIN job_tracking jt ON jt.job_id = j.id inner JOIN action a ON a.id = jt.action_id WHERE j.user_id = :user AND ( jt.created_at = ( SELECT MAX(jt2.created_at) FROM job_tracking jt2 WHERE jt2.job_id = j.id ) )  GROUP BY j.id ORDER BY maxCreatedAt DESC;";



        $stmt = $this->connection->executeQuery($sql, ['user' => $user->getId(),]);

        return $stmt->fetchAllAssociative();
    }

    public function findJobsInProgressOrClosedByUser(User $user, $inProgress = true): ?array
    {


        $sql = "SELECT j.id, j.title, j.created_at, j.recruiter, a.name as action_name, a.set_closed, MAX(jt.created_at) AS maxCreatedAt, j.offer_description AS description, DATEDIFF( CURRENT_DATE, MAX(jt.created_at) ) AS delai , count(note.id) as note_count FROM job j inner JOIN job_tracking jt ON jt.job_id = j.id inner JOIN action a ON a.id = jt.action_id left join note on note.job_id = j.id WHERE j.user_id =  :user AND ( jt.created_at = ( SELECT MAX(jt2.created_at) FROM job_tracking jt2 WHERE jt2.job_id = j.id ) ) AND ( (:inProgress = 1 AND (jt.id IS NULL OR a.set_closed = 0 OR a.set_closed IS NULL)) OR (:inProgress = 0 AND (jt.id IS NOT NULL AND a.set_closed = 1)) ) GROUP BY j.id, a.name, a.set_closed, j.title, j.created_at, j.recruiter ORDER BY delai DESC;";



        $stmt = $this->connection->executeQuery($sql, ['user' => $user->getId(), 'inProgress' => $inProgress]);

        return $stmt->fetchAllAssociative();
    }



    public function getJobsPerMonth(User $user)
    {

        $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS yearmonth,
    COUNT(*) AS count  FROM `job` WHERE user_id = :user GROUP BY
    yearmonth
ORDER BY
    yearmonth;";


        $stmt = $this->connection->executeQuery($sql, ['user' => $user->getId()]);

        return $stmt->fetchAllAssociative();
    }

    public function getClosedJobsPerMonth(User $user)
    {

        $sql = "SELECT  count(*) as count, DATE_FORMAT(job_tracking.created_at, '%Y-%m') AS yearmonth, SUM(action.set_closed) as set_closed  FROM `job` INNER join job_tracking on job_tracking.job_id = job.id inner join action on job_tracking.action_id = action.id WHERE job.user_id = :user GROUP BY yearmonth HAVING set_closed > 0 ORDER BY yearmonth;";

        $sql = "SELECT  DATE_FORMAT(created_at, '%Y-%m') yearmonth,  COUNT(*) AS count  from job_tracking INNER join action on action.id = job_tracking.action_id WHERE action.set_closed = 1 GROUP BY
    yearmonth";

        $stmt = $this->connection->executeQuery($sql, ['user' => $user->getId()]);

        return $stmt->fetchAllAssociative();
    }

    public function isClosedJob(Job $job)
    {
        $sql = "select sum(action.set_closed) as sum from job_tracking INNER join action on action.id = job_tracking.action_id where job_tracking.job_id = :job;";
        $stmt = $this->connection->executeQuery($sql, params: ['job' => $job->getId()]);

        $queryArr =  $stmt->fetchAllAssociative()[0];
        return !!$queryArr['sum'];
    }

    public function getJobSourceCountByUser(User $user)
    {
        $sql = "SELECT job_source.name, COUNT(*)  as count FROM `job` inner join job_source on job.source_id = job_source.id where job.user_id = :user GROUP by job.source_id;";
        $stmt = $this->connection->executeQuery($sql, ['user' => $user->getId()]);

        return $stmt->fetchAllAssociative();
    }

    public function getCurrentWeekJob(User $user){
        $sql = "SELECT job.created_at,  count(*) as count FROM `job` WHERE job.user_id = :user and DATEDIFF( NOW(), job.created_at) < 7 group by job.created_at;";
        $stmt = $this->connection->executeQuery($sql, ['user' => $user->getId()]);

        return $stmt->fetchAllAssociative();
    }

    public function getClosedAvgDelai(User $user){
        $sql = "SELECT AVG(delai) AS average_delai FROM ( SELECT DATEDIFF(MAX(jt.created_at), j.created_at) AS delai FROM job j INNER JOIN job_tracking jt ON jt.job_id = j.id INNER JOIN action a ON a.id = jt.action_id WHERE j.user_id = :user and a.set_closed =1 GROUP BY j.id ) AS subquery;";
        $stmt = $this->connection->executeQuery($sql, ['user' => $user->getId()]);

        return $stmt->fetchOne();
    }

    public function getLonguestDelai(User $user){
        $sql = 'SELECT id, recruiter,title, count_action, MAX(delai) AS delai , set_closed FROM ( SELECT j.id, j.recruiter, j.title, count(jt.action_id) as count_action,a.set_closed ,DATEDIFF(MAX(jt.created_at), j.created_at) AS delai FROM job j INNER JOIN job_tracking jt ON jt.job_id = j.id INNER JOIN action a ON a.id = jt.action_id WHERE j.user_id = :user GROUP BY j.id) AS subquery;';

        $stmt = $this->connection->executeQuery($sql, ['user' => $user->getId()]);
        return $stmt->fetchAssociative();
    }

    public function getMostProlificWeekDay(User $user)
    {
        $sql = 'SELECT jour, max(day_count) day_count from (select Weekday(j.created_at) jour, count(Weekday(j.created_at)) day_count from job j WHERE user_id = :user GROUP by jour) as subquery;';
        $stmt = $this->connection->executeQuery($sql, ['user' => $user->getId()]);
        return $stmt->fetchAssociative();
    }

    public function getMostProlificDay(User $user){
        $sql = 'Select  created_at, count(created_at) job_count FROM job where user_id = :user GROUP by created_at order by job_count desc LIMIT 1; ';
        $stmt = $this->connection->executeQuery($sql, ['user' => $user->getId()]);
        return $stmt->fetchAssociative();
    }

}
