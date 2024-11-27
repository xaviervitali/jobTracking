<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class DelaiFilter extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('formatDelay', [$this, 'formatDelay']),
        ];
    }

    public function formatDelay($delay)
    {


        if (
            gettype($delay) === "string"
            && $delay !== '0'
            && intval($delay) === 0
        ) {
            return $delay;

        }

        // On est sûr que c'est un int !!!

        if ($delay === 0) {
            return "aujourd'hui";
        }


        $absDelay = abs($delay);
        if($absDelay === 1){
            return $delay === -1 ? 'demain' : 'hier';
        }

        $jourStr = "jours";

        $sentence = ($delay > 0 ? "il y a " : "dans") . " $absDelay $jourStr";

        return $sentence;
    }

}