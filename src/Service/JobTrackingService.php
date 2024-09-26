<?php

namespace App\Service;


class JobTrackingService
{

    public function getActionsCountForJobs(array $jobs,  string $noActionLabel = 'Attente réponse candidature'):array
    {

        $actions = [$noActionLabel => 0];

        foreach ($jobs as $job) {
            $jobTrackings = $job->getJobTracking();

            if (empty($jobTrackings->toArray())) {
                $actions[$noActionLabel]++;
                continue;
            }
            foreach($jobTrackings as $jobTracking){
                $action = $jobTracking->getAction()->getName();
                if (array_key_exists($action, $actions)) {
                    $actions[$action]++;
                    continue;
                }
                $actions[$action] = 1;
            }
        }

        return $actions;
    }

}
