<?php

namespace App\Repository;

use App\Entity\JobTracking;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


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


}
