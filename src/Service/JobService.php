<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\JobRepository;
use DateTime;
use DateTimeImmutable;

class JobService
{
    private DateTimeImmutable $minDate;
    private $userJobs = [];

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
        if (!empty($this->userJobs)) {
            return $this->userJobs;
        }

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
        $this->userJobs = $userJobs;
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
        $history = [];
        $allJobs = $this->getJobsByUser();
        $months = array_values(array_unique(array_map(function ($job) {
            $date = $job['created_at'];
            return $date->format('Y-m');
        }, $allJobs)));

        foreach ($months as $month) {
            $current = [];
            $current['yearmonth'] = $month;
            $current['count'] = count(array_filter(
                $allJobs,
                function ($job) use ($month) {
                    $date = $job['created_at'];
                    $jobMonth = $date->format('Y-m');
                    return $jobMonth === $month;
                }
            ));
            $history[] = $current;
        }
        return $this->fillJobsPerMonth($history);
    }

    public function findOldestJob()
    {
        $allJobs = $this->getJobsByUser();


        usort($allJobs, function ($a, $b) {
            return $a['max_created_at'] <=> $b['max_created_at'];
        });

        return $allJobs[0];
    }

    public function getClosedJobsPerMonth()
    {

        $history = [];
        $allJobs = $this->getJobsByUser();
        $months = array_values(array_unique(array_map(function ($job) {
            $date = $job['max_created_at'];
            return $date->format('Y-m');
        }, $allJobs)));

        foreach ($months as $month) {
            $current = [];
            $current['yearmonth'] = $month;
            $current['count'] = count(array_filter(
                $allJobs,
                function ($job) use ($month) {
                    $date = $job['max_created_at'];
                    $jobMonth = $date->format('Y-m');
                    return $jobMonth === $month && !!$job['set_closed'];
                }
            ));
            $history[] = $current;
        }
        return $this->fillJobsPerMonth($history);


    }

    public function getCurrentWeekJobs()
    {
        // $jobsPerMonth = $this->jobRepository->getCurrentWeekJob($this->user);
        $allJobs = $this->getJobsByUser();

        $jobsPerWeek = [];
        $weekDates = [];

        $start = new DateTime();
        $start->modify('-1 week');
        $end = new DateTime();


        while ($start < $end) {
            $weekDates[] = $start->format('Y-m-d');
            $start->modify('+1 day');
        }

        foreach ($weekDates as $weekDate) {
            $currentDay = array_filter(
                $allJobs,
                function ($job) use ($weekDate) {
                    $createdAt = $job['created_at'];
                    return $createdAt->format('Y-m-d') === $weekDate;
                }
            );

            $jobsPerWeek[$weekDate] = count($currentDay);
        }

        return $jobsPerWeek;

    }


    public function getJobSourceCountByUser()
    {
        $jobSources = [];
        $allJobs = $this->getJobsByUser();
        $sourceNames = array_values(array_unique(array_map(function ($job) {
            return $job['source_name'];
        }, $allJobs)));

        foreach ($sourceNames as $source) {
            $current = [];
            $current['name'] = $source;
            $current['count'] = count(array_filter($allJobs, function ($job) use ($source) {
                return $job['source_name'] === $source;
            }));
            $jobSources[] = $current;
        }

        return $jobSources;

    }

    public function getClosedAvgDelai()
    {
        $allJobs = $this->getJobsByUser();
        $closedJobs = array_values(array_filter($allJobs, function ($job) {
            return !!$job['set_closed'];
        }));

        $delays = array_column($closedJobs, 'delai');
        return round(array_sum($delays) / count($closedJobs), 2);

    }

    public function getLonguestDelai()
    {
        $allJobs = $this->getJobsByUser();
        usort($allJobs, function ($a, $b) {
            return $b['delai'] <=> $a['delai'];
        });

        return $allJobs[0];
    }

    public function getMostProlificWeekDay()
    {
        $userWeekDay = 0;
        $max = 0;


        $allJobs = $this->getJobsByUser();
        $weekDays = array_values(array_unique(array_map(function ($job) {
            $date = $job['created_at'];
            return $date->format('w');
        }, $allJobs)));

        foreach ($weekDays as $weekDay) {

            $count = count(array_filter(
                $allJobs,
                function ($job) use ($weekDay) {
                    $date = $job['created_at'];
                    $jobDay = $date->format('w');
                    return $jobDay === $weekDay;
                }
            ));


            if ($count > $max) {
                $max = $count;
                $userWeekDay = $weekDay;
            }
        }

        return intval($userWeekDay);


    }

    public function getMostProlificDay()
    {
        $allJobs = $this->getJobsByUser();
        $day = null;
        $dayCount = 0;

        foreach ($allJobs as $currentJob) {

            $createdAt = $currentJob['created_at']->format('Y-m-d');

            if ($createdAt !== $day) {
                $jobs = array_values(array_filter($allJobs, function ($job) use ($createdAt) {
                    return $job['created_at']->format('Y-m-d') === $createdAt;
                }));

                if (count($jobs) > $dayCount) {
                    $day = $createdAt;
                    
                    $dayCount = count($jobs);
                }
            }
        }

        return ['created_at' => $day, 'job_count' => $dayCount];

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



}
