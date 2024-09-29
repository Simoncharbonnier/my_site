<?php

namespace App\Service;

class DateFormatter
{
    public function format($month, $day): string
    {
        $months = [
            1 => 'janvier',
            2 => 'février',
            3 => 'mars',
            4 => 'avril',
            5 => 'mai',
            6 => 'juin',
            7 => 'juillet',
            8 => 'août',
            9 => 'septembre',
            10 => 'octobre',
            11 => 'novembre',
            12 => 'décembre'
        ];

        $dayByMonth = [
            1 => 31,
            2 => 29,
            3 => 31,
            4 => 30,
            5 => 31,
            6 => 30,
            7 => 31,
            8 => 31,
            9 => 30,
            10 => 31,
            11 => 30,
            12 => 31
        ];

        if (empty($months[intval($month)]) || $dayByMonth[intval($month)] < $day) {
            return false;
        }

        return $day . ' ' . $months[intval($month)];
    }
}
