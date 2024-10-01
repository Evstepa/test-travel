<?php

namespace App\Service;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Calculation extends AbstractController
{
    public function calcDiscount(
        int $basedSum,
        string $participantBirth,
        string $travelStartDate,
        string $paymentDate = null,
    ): array {

        $age = $this->calcAge($participantBirth);
// dd($age);
        $newSum = $basedSum;



        return [
            'status' => 200,
            'body' => [
                'summa' => $newSum,
            ],
        ];
    }

    private function calcAge(string $date): int {
        $day = explode('.', $date)[0];
        $month = explode('.', $date)[1];
        $year = explode('.', $date)[2];
        if($month > date('m') || $month == date('m') && $day > date('d'))
            return date('Y') - $year - 1;
        else
            return date('Y') - $year;
    }
}