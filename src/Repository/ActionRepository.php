<?php

namespace App\Repository;

use App\Entity\Action;
use App\Entity\User;
use App\Enums\ActionStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Answer>
 */
class ActionRepository extends ServiceEntityRepository
{
    private  $connection;
    public function __construct(ManagerRegistry $registry, Connection $connection)
    {
        parent::__construct($registry, Action::class);
        $this->connection = $connection;
    }

    public function getActionCountAndRatioByUser(User $user){
        $sql = "SELECT action.name, COUNT(*) AS count, COUNT(*) / ( SELECT COUNT(*) FROM job inner join job_tracking jt INNER JOIN action a ON a.id = jt.action_id WHERE jt.job_id = job.id AND a.name = :notActionName ) AS ratio FROM job INNER JOIN job_tracking ON job_tracking.job_id = job.id INNER JOIN action ON action.id = job_tracking.action_id WHERE job.user_id = :user AND NOT (action.name = :notActionName) GROUP BY action.name, job.id;";

        $stmt = $this->connection->executeQuery($sql, [
            'user' => $user->getId(),
            'notActionName' => ActionStatus::getStartActionName() ]);

        return $stmt->fetchAllAssociative();

    }
}
