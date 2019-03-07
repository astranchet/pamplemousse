<?php

namespace Pamplemousse\Twig_Extension;

use DateTime;
use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

class DatesFilter extends Twig_Extension
{

    public function getName()
    {
        return 'dates';
    }

    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('timeago', [$this, 'timeagoFilter']),
            new Twig_SimpleFilter('age_caption', [$this, 'ageCaptionFilter']),
        ];
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('toggle_filters', [$this, 'toggleFilters']),
        ];
    }

    public function toggleFilters($parameters, $filter)
    {
        if (is_null($parameters)) {
            $parameters = array();
        }

        // Remove value if it exists
        foreach($parameters as $key => $value) {
            if ($filter == $value) {
                unset($parameters[$key]);
                return $parameters;
            }
        }

        // Else add it
        $parameters[] = $filter;

        return $parameters;
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

    public function ageCaptionFilter($photo)
    {
        $datetime = new DateTime($photo->date_taken);

        $captions = [];
        foreach ($photo->kids as $id => $kid) {
            $config = $kid->getConfig();
            $captions[$kid->kid] = $this->ageFilter($datetime, $config['pregnancydate'], $config['birthdate']);
        }

        if(sizeof($captions) == 1) {
            return array_pop($captions);
        } else {
            $caption = '';
            foreach ($captions as $name => $age)
                $captions[$name] = sprintf("<b>%s</b> : %s", $name, $age);

            return implode(" ; ", $captions);
        }
    }

    public function ageFilter($datetime, $pregnancydate, $birthdate)
    {     
        $daysToBirth = intval((new DateTime($birthdate))->diff($datetime)->format('%R%a'));
        $daysToPregnancy = intval((new DateTime($pregnancydate))->diff($datetime)->format('%R%a'));

        if (!is_null($birthdate) && $daysToBirth == 0) {
            return 'Le jour J !';
        } elseif (is_null($birthdate) || $daysToBirth < 0) {
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
