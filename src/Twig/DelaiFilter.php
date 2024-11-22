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
        

        if (gettype($delay) === "string") {
            return $delay;
            
        }
        
        $sentence = "aujourd'hui";
        $absDelay = abs($delay);
        $jourStr = "jour";

        if ($absDelay >= 1) {
            $jourStr .= "s";
        }

        if ($delay > 0) {
            $sentence = "il y a $absDelay $jourStr";
        }

        if ($delay < 0) {
            $sentence = "dans $absDelay $jourStr";
        }

        return $sentence;
    }

}