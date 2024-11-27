<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\JobTrackingRepository;

class JobTrackingService
{
    public function __construct(private User $user, private JobTrackingRepository $jobTrackingRepository)
    {

    }

    public function getActionCount()
    {
        $userActionCount = [];

        $allJobtracking = $this->getJobTrackingByUser();
        $allActionNames = array_unique(array_map(function ($jobTracking) {
            return $jobTracking->getAction()->getName();
        }, $allJobtracking));
        foreach ($allActionNames as $actionName) {
            $currentAction = [];
            $currentAction['name'] = $actionName;
            $currentAction['count'] = count(array_filter($allJobtracking, function ($jobTracking) use ($actionName) {
                return $jobTracking->getAction()->getName() === $actionName;
            }));
            $userActionCount[] = $currentAction;
        }
        return $userActionCount;
    }

    public function getJobTrackingByUser(): array
    {
        $userJobTracking = $this->jobTrackingRepository->findBy(['user' => $this->user]);
        return $userJobTracking;
    }

    public function getJobClosedActions(){
        $userActionCount = [];

        $allJobtracking = $this->getJobTrackingByUser();
        $allActionNames = array_unique(array_map(function ($jobTracking) {
            return  $jobTracking->getAction()->getName() ;
        }, $allJobtracking));

        foreach ($allActionNames as $actionName) {
            $currentAction = [];
            $currentAction['name'] = $actionName;
            $currentAction['count'] = count(array_filter($allJobtracking, function ($jobTracking) use ($actionName) {
                return $jobTracking->getAction()->isSetClosed()  &&  $jobTracking->getAction()->getName() === $actionName;
            }));
            $userActionCount[] = $currentAction;
        }
        return array_values(array_filter($userActionCount, function ($action) {
         return    $action['count'] > 0;
        }));
    }
}