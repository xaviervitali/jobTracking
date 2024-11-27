<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\JobRepository;
use DateTime;
use DateTimeImmutable;

class JobService
{
    private array $delays = [];
    private DateTimeImmutable $minDate;


    public function __construct(private User $user, DateTimeImmutable $minDate, private JobRepository $jobRepository, )
    {
        $this->minDate = $minDate;
    }


    public function getJobsByUser()
    {
        $userJobs = [];
        $userJobsRepo = $this->jobRepository->findBy(['user' => $this->user]);

        foreach ($userJobsRepo as $userJob) {
            $job = [];

            $jobTrackingsArray = $userJob->getJobTracking()->toArray();
            usort($jobTrackingsArray, function ($a, $b) {
                return $b->getCreatedAt() <=> $a->getCreatedAt();
            });
            $lastJobTracking = $jobTrackingsArray[0];

            $action = $lastJobTracking->getAction();
            $maxCreatedAt = $lastJobTracking->getCreatedAt();
            
            if ($action->isSetClosed()) {
                $origin = $userJob->getCreatedAt();
                $interval = $origin->diff($maxCreatedAt);
            } else {
                $target = new DateTimeImmutable();
                $interval = $maxCreatedAt->diff($target);
            }

            $job['id'] = $userJob->getId();
            $job['recruiter'] = $userJob->getRecruiter();
            $job['title'] = $userJob->getTitle();
            $job['name'] = $action->getName();
            $job['set_closed'] = boolval($action->isSetClosed());
            $job['created_at'] = $maxCreatedAt;
            $job['delai'] = $interval->format('%a');
            $job['note_count'] = count($userJob->getNotes());
            $userJobs[] = $job;
        }
        return $userJobs;

    }


    public function getJobsPerMonth()
    {
        $jobsPerMonth = $this->jobRepository->getJobsPerMonth($this->user);
        return $this->fillJobsPerMonth($jobsPerMonth);
    }

    public function getClosedJobsPerMonth()
    {
        $jobsPerMonth = $this->jobRepository->getClosedJobsPerMonth($this->user);
        return $this->fillJobsPerMonth($jobsPerMonth);
    }

    public function getCurrentWeekJobs()
    {
        $jobsPerMonth = $this->jobRepository->getCurrentWeekJob($this->user);
        return $this->fillJobsWeek($jobsPerMonth);
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

            $currentMonth = array_filter(
                $jobs,
                function ($job) use ($month) {
                    return $job['yearmonth'] === $month;
                }
            );

            if (count($currentMonth) > 0) {
                $count = $currentMonth[array_key_first($currentMonth)]['count'];
            }
            $jobsPerMonth[$month] = $count;


        }
        return $jobsPerMonth;
    }

    private function fillJobsWeek($jobs)
    {

        $jobsPerWeek = [];
        $weekDates = [];
        $start = new DateTime();
        $start->modify('-1 week');
        $end = new DateTime();

        while ($start < $end) {
            $weekDates[] = $start->format('Y-m-d');
            $start->modify('+1 day'); // Ajoute un mois
        }

        foreach ($weekDates as $weekDate) {
            $currentDay = array_filter(
                $jobs,
                function ($job) use ($weekDate) {
                    $createdAt = new DateTime($job['created_at']);
                    return $createdAt->format('Y-m-d') === $weekDate;
                }
            );
            $count = 0;
            if (count($currentDay) > 0) {
                $count = $currentDay[array_key_first($currentDay)]['count'];
            }
            $jobsPerWeek[$weekDate] = $count;
            ;
        }

        return $jobsPerWeek;

    }

}
