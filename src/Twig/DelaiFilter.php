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
        
        if ($delay === 0) {
            return "aujourd'hui";
        }

        $absDelay = abs($delay);
        $jourStr = "jour";

        if ($absDelay > 1) {
            $jourStr .= "s";
        }

        $sentence = ($delay > 0 ? "il y a " : "dans") . " $absDelay $jourStr";

        return $sentence;
    }

}