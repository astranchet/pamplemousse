<?php

namespace Pamplemousse\Twig_Extension;

use Twig_Extension;
use Twig_SimpleFilter;

class TimeagoFilter extends Twig_Extension
{

    public function getName()
    {
        return 'timeago';
    }

    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('timeago', [$this, 'timeagoFilter']),
        ];
    }

    public function timeagoFilter($datetime)
    {
        $time = time() - strtotime($datetime); 

        $units = [
            31536000 => ['année','années'],
            2592000 => ['mois','mois'],
            604800 => ['semaine','semaines'],
            86400 => ['jour','jours'],
            3600 => ['heure','heures'],
            60 => ['minute','minutes'],
            1 => ['seconde','secondes']
        ];

        foreach ($units as $unit => $labels) {
            if ($time < $unit) continue;
            $numberOfUnits = floor($time / $unit);
            $labelSingular = $labels[0];
            $labelPlurar = $labels[1];
            if ($labelSingular == 'seconde') {
                return "À l'instant";
            } else {
                return sprintf("Il y a %s %s", $numberOfUnits, ($numberOfUnits == 1)? $labelSingular : $labelPlurar);
            }
        }

        return $time;
    }

}
