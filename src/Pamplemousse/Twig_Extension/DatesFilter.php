<?php

namespace Pamplemousse\Twig_Extension;

use DateTime;
use Twig_Extension;
use Twig_SimpleFilter;

class DatesFilter extends Twig_Extension
{
    protected $birthdate, $pregnancydate;

    public function __construct($config)
    {
        $this->birthdate = new DateTime($config['kid']['birthdate']);
        $this->pregnancydate = new DateTime($config['kid']['pregnancydate']);
    }

    public function getName()
    {
        return 'dates';
    }

    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('timeago', [$this, 'timeagoFilter']),
            new Twig_SimpleFilter('age', [$this, 'ageFilter']),
        ];
    }

    public function timeagoFilter($datetime)
    {
        $time = time() - strtotime($datetime); 

        $units = [
            31536000 => ['an','ans'],
            2592000 => ['mois','mois'],
            604800 => ['semaine','semaines'],
            86400 => ['jour','jours'],
            3600 => ['heure','heures'],
            60 => ['minute','minutes'],
            1 => ['seconde','secondes']
        ];

        foreach ($units as $unit => $labels) {
            if ($time < $unit) continue;
            $labelSingular = $labels[0];
            $labelPlural = $labels[1];

            if ($labelSingular == 'seconde') {
                return "À l'instant";
            } elseif (in_array($labelSingular, ['mois', 'an'])) {
                $numberOfUnits = round(($time / $unit) * 2) / 2;
                if ($numberOfUnits == floor($numberOfUnits)) {
                    return sprintf("Il y a %s %s", $numberOfUnits, ($numberOfUnits == 1)? $labelSingular : $labelPlural);
                } else {
                    $numberOfUnits = floor($numberOfUnits);
                    return sprintf("Il y a %s %s et demi", $numberOfUnits, ($numberOfUnits == 1)? $labelSingular : $labelPlural);
                }
            } else {
                $numberOfUnits = floor($time / $unit);
                return sprintf("Il y a %s %s", $numberOfUnits, ($numberOfUnits == 1)? $labelSingular : $labelPlural);
            }
        }

        return $time;
    }

    public function ageFilter($datetime)
    {
        $datetime = new DateTime($datetime);

        $daysToBirth = intval($this->birthdate->diff($datetime)->format('%R%a'));
        $daysToPregnancy = intval($this->pregnancydate->diff($datetime)->format('%R%a'));

        if ($daysToBirth == 0) {
            return 'Le jour J !';
        } elseif ($daysToBirth < 0) {
            return $this->pregnancyAgeFilter($daysToPregnancy);
        } elseif ($daysToBirth < 367*3) {
            return $this->babyAgeFilter($daysToBirth);
        // } elseif ($daysToBirth < 364*3) {
        //     return $this->toddlerAgeFilter($daysToBirth);
        } else {
            return $this->kidAgeFilter($daysToBirth);
        }
    }

    private function pregnancyAgeFilter($daysToPregnancy)
    {
        $numberOfMonths = round(($daysToPregnancy/30) * 2) / 2;
        if ($numberOfMonths != floor($numberOfMonths)) {
            $numberOfMonths = sprintf("%s mois et demi", floor($numberOfMonths));
        } else {
            $numberOfMonths = sprintf("%s mois", floor($numberOfMonths));
        }
        return sprintf("À %s de grossesse", $numberOfMonths);
    }

    /**
     * Baby age is counted with days/weeks/months
     */
    private function babyAgeFilter($daysToBirth)
    {
        $units = [
            365 => ['an','ans'],
            30 => ['mois','mois'],
            7 => ['semaine','semaines'],
            1 => ['jour','jours']
        ];

        foreach ($units as $unit => $labels) {
            if ($daysToBirth < $unit) continue;

            $labelSingular = $labels[0];
            $labelPlural = $labels[1];
            $prefix = "";

            if (in_array($labelSingular, ['mois', 'an'])) { // Those can be counted in half
                $numberOfUnits = round(($daysToBirth / $unit) * 2) / 2;
                if ($daysToBirth < $unit*$numberOfUnits) {
                        $prefix = "Bientôt ";
                    }
                if ($numberOfUnits == floor($numberOfUnits)) {
                    return $prefix.sprintf("%s %s", $numberOfUnits, ($numberOfUnits == 1)? $labelSingular : $labelPlural);
                } else {
                    $numberOfUnits = floor($numberOfUnits);
                    return $prefix.sprintf("%s %s et demi", $numberOfUnits, ($numberOfUnits == 1)? $labelSingular : $labelPlural);
                }
            } else {
                $numberOfUnits = floor($daysToBirth / $unit);
                return sprintf("%s %s", $numberOfUnits, ($numberOfUnits == 1)? $labelSingular : $labelPlural);
            }
        }
    }

    /**
     * Toddler age is counted in months
     */
    private function toddlerAgeFilter($daysToBirth)
    {
        $numberOfMonths = round(($daysToBirth/30) * 2) / 2;
        $numberOfYears = round(($numberOfMonths/12) * 2) / 2;

        if (floor($numberOfMonths)/12 == $numberOfYears) {
            return sprintf("%s ans", $numberOfYears);
        } else {
            return sprintf("%s mois", floor($numberOfMonths));
        }
    }

    /**
     * Kid age is counted in years (and half)
     */
    private function kidAgeFilter($daysToBirth)
    {
        $numberOfMonths = round(($daysToBirth/30) * 2) / 2;
        $numberOfYears = round(($numberOfMonths/12) * 2) / 2;

        if ($numberOfYears != floor($numberOfYears)) {
            return sprintf("%s ans et demi", floor($numberOfYears));
        } else {
            return sprintf("%s ans", floor($numberOfYears));
        }
    }

}
