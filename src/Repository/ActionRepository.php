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

    public function getActionCountAndRatioByUser(User $user, $closed = false)
    {
        $sql = "SELECT a.name, COUNT(*) AS count, COUNT(*) / (
                    SELECT COUNT(*)
                    FROM jt_job j
                    INNER JOIN jt_job_tracking jt ON jt.job_id = j.id
                    INNER JOIN jt_action a ON a.id = jt.action_id
                    WHERE jt.job_id = j.id AND a.name = :notActionName
                ) AS ratio
                FROM jt_job j
                INNER JOIN jt_job_tracking jt ON jt.job_id = j.id
                INNER JOIN jt_action a ON a.id = jt.action_id
                WHERE j.user_id = :user
                  AND a.name != :notActionName
                  AND a.set_closed = :closed
                GROUP BY a.name;";

        $stmt = $this->connection->executeQuery($sql, [
            'user' => $user->getId(),
            'notActionName' => ActionStatus::getStartActionName(),
            'closed' => $closed
        ]);

        return $stmt->fetchAllAssociative();
    }


}
