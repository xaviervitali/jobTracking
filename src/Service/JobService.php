<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\JobRepository;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;

class JobService
{
    private array $delays  = [];
    private DateTimeImmutable $minDate;


    public function __construct(private User $user, DateTimeImmutable $minDate, private JobRepository $jobRepository,)
    {
        $this->minDate = $minDate;
    }





    public function getJobsInProgress()
    {
 
        return     $this->mapFindJobsInProgressOrClosedByUserRepo($this->jobRepository->findJobsInProgressOrClosedByUser($this->user));
    }

    public function getClosedJob()
    {
        return  $this->mapFindJobsInProgressOrClosedByUserRepo($this->jobRepository->findJobsInProgressOrClosedByUser($this->user, false));
    }

    public function  getJobsPerMonth()
    {
        $jobsPerMonth = $this->jobRepository->getJobsPerMonth($this->user);
        return $this->fillJobsPerMonth($jobsPerMonth);
    }

    public function  getClosedJobsPerMonth()
    {
        $jobsPerMonth = $this->jobRepository->getClosedJobsPerMonth($this->user);
        return $this->fillJobsPerMonth($jobsPerMonth);
    }

    public function getDelays()
    {
        return $this->delays;
    }

    private function getDateBetween()
    {
        $completeDates = [];
        $start = DateTime::createFromImmutable($this->minDate);
        $end = new DateTime();

        // Ajoute tous les mois entre les deux dates, y compris la date de début
        while ($start <= $end) {
            $completeDates[] = $start->format('Y-m');
            $start->modify('+1 month'); // Ajoute un mois
        }
        return $completeDates;
    }
    private function fillJobsPerMonth($jobs)
    {

        $jobsPerMonth = [];

        foreach ($this->getDateBetween() as $month) {

            $count = 0;
        
          $currentMonth =  array_filter(
                 $jobs,
                function ( $job) use ($month) {
                    return $job['yearmonth'] === $month;
                }
            );

            if(count( $currentMonth )>0){
                $count = $currentMonth[array_key_first($currentMonth)]['count'];
            }
            $jobsPerMonth[$month] = $count;

         
        }
        return $jobsPerMonth;
    }

    private function mapFindJobsInProgressOrClosedByUserRepo($repo)
    {
        $jobs = [];
        foreach ($repo as $job) {
            $jobs[] = $job[0];
        }

        return $jobs;
    }
}
