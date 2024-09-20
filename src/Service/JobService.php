<?php

namespace App\Service;

use App\Entity\Job;
use App\Entity\JobTracking;
use App\Entity\User;
use App\Repository\JobRepository;
use DateTime;
use DateTimeImmutable;

use function PHPUnit\Framework\isEmpty;

class JobService
{
    private array $jobs;

    private array $jobsInProgress = [];
    private array $closedJobs = [];
    private array $delays  = [];
    private array $jobsPerMonth;

    private array $closedJobsPerMonth;
    private DateTimeImmutable $minDate;


    public function __construct(User $user, DateTimeImmutable $minDate, private JobRepository $jobRepository,)
    {
        $this->minDate = $minDate;
        $this->jobs = $jobRepository->findByUserAndMoreThanDate($user, $minDate);
        $this->setValues();
    }




    public function getJobsInProgress()
    {
        return     $this->jobsInProgress;
    }

    public function getClosedJob()
    {
        return     $this->closedJobs;
    }

    public function  getJobsPerMonth()
    {
        return $this->jobsPerMonth;
    }

    public function  getlosedJobsPerMonth()
    {
        return $this->closedJobsPerMonth;
    }

    public function getDelays()
    {
        return $this->delays;
    }


    private function  setValues()
    {
        foreach ($this->jobs as $job) {
            $closedJobTracking = array_filter(
                $job->getJobTracking()->toArray(),
                function (JobTracking $jobTracking) {
                    return !!$jobTracking->getAction()->isSetClosed();
                }
            );
            $isClosed = count($closedJobTracking) > 0;

            if ($isClosed) {
                $this->closedJobs[] = $job;
                $origin = $job->getCreatedAt();
                $target = $closedJobTracking[0]->getCreatedAt();
                $this->delays[] =  intval($origin->diff($target)->format('%a'));
            } else {
                $this->jobsInProgress[] = $job;
            }
        }
        $this->setJobsPerMonth();
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
    private function setJobsPerMonth()
    {
        foreach ($this->getDateBetween() as $month) {
            $this->jobsPerMonth[$month] = count(array_filter(
                $this->jobs,
                function (Job $job) use ($month) {
                    return $job->getCreatedAt()->format('Y-m') == $month;
                }
            ));

            $this->closedJobsPerMonth[$month] = count(array_filter(
                $this->closedJobs,
                function (Job $job) use ($month) {

                    return $job->getCreatedAt()->format('Y-m') == $month;
                }
            ));
        }
    }
}
