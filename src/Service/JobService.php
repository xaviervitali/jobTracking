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


    public function __construct(private User $user, private JobRepository $jobRepository, )
    {

    }
    public function setMinDate(DateTimeImmutable $minDate)
    {
        $this->minDate = $minDate;
        return $this;
    }


    public function getJobsByUser()
    {
        $userJobs = [];
        $userJobsRepo = $this->jobRepository->findBy(['user' => $this->user]);

        foreach ($userJobsRepo as $userJob) {
            $job = [];

            $jobTrackingsArray = $userJob->getJobTracking()->toArray();


            $isFuture = false;
            $isClosedJob = array_values(array_filter($jobTrackingsArray, function ($jobTracking) {
                return $jobTracking->getAction()->isSetClosed();
            }));
            if (!empty($isClosedJob)) {
                $target = $userJob->getCreatedAt();
                $maxCreatedAt = $isClosedJob[0]->getCreatedAt();
                $maxCreatedAt = $maxCreatedAt->setTime(0, 0);
                $action = $isClosedJob[0]->getAction();

            } else {

                usort($jobTrackingsArray, function ($a, $b) {
                    return $b->getCreatedAt() <=> $a->getCreatedAt();
                });
                $lastJobTracking = $jobTrackingsArray[0];

                $action = $lastJobTracking->getAction();
                $maxCreatedAt = $lastJobTracking->getCreatedAt();
                $maxCreatedAt = $maxCreatedAt->setTime(0, 0);

                $date = new DateTimeImmutable();
                $date = $date->setTime(0, 0);
                $target = $date;
                $isFuture = $maxCreatedAt > $target;
            }

            $interval = $maxCreatedAt->diff($target);
            $daysDiff = $interval->days;

            if ($interval->h > 0 || $interval->i > 0 || $interval->s > 0) {
                $daysDiff++;
            }
            
            // Ajuster la différence pour les dates dans le futur
            if ($isFuture) {
                $daysDiff = -$daysDiff;
            }



            $job['id'] = $userJob->getId();
            $job['recruiter'] = $userJob->getRecruiter();
            $job['created_at'] = $userJob->getCreatedAt();
            $job['title'] = $userJob->getTitle();
            $job['action_name'] = $action->getName();
            $job['set_closed'] = boolval($action->isSetClosed());
            $job['max_created_at'] = $maxCreatedAt;
            $job['delai'] = $daysDiff;
            $job['note_count'] = count($userJob->getNotes());
            $job['source_name'] = $userJob->getSource()->getName();

            $userJobs[] = $job;
        }
        return $userJobs;

    }

    public function getJobsInProgressByUser()
    {
        $allJobs = $this->getJobsByUser();
        $inProgress = array_values(array_filter($allJobs, function ($job) {
            return !$job['set_closed'];
        }));

        usort($inProgress, function ($a, $b) {
            return $a['max_created_at'] <=> $b['max_created_at'];
        });

        return $inProgress;
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
