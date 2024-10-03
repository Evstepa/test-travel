<?php

namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Calculation extends AbstractController
{
    /**
     * определение суммы скидки
     * @param int $basedSum
     * @param string $participantBirth
     * @param string $travelStartDate
     * @param string $paymentDate
     * @return array
     */
    public function calcDiscount(
        int $basedSum,
        string $participantBirth,
        string $travelStartDate,
        string $paymentDate = null,
    ): array {
        $age = $this->calcAge($participantBirth, $travelStartDate);

        $newSum = $this->calcAgeSum($age, $basedSum);
        if ($newSum === 0 || is_null($paymentDate)) {
            return [
                'status' => 200,
                'body' => [
                    'summa' => $newSum,
                ],
            ];
        }

        $discount = $this->calcBooking($paymentDate, $travelStartDate);
        $newSum = ($newSum * $discount / 100) < 1500
            ? $newSum * (1 - $discount / 100)
            : $newSum - 1500;

        return [
            'status' => 200,
            'body' => [
                'summa' => $newSum,
            ],
        ];
    }

    /**
     * определение возраста на момент старта путешествия
     * @param string $date
     * @param string $travelStartDate
     * @return int
     */
    public function calcAge(string $birthDate, string $travelStartDate): int
    {
        $birth = [
            'day' => (int) explode('.', $birthDate)[0],
            'month' => (int) explode('.', $birthDate)[1],
            'year' => (int) explode('.', $birthDate)[2],
        ];
        $travel = [
            'day' => (int) explode('.', $travelStartDate)[0],
            'month' => (int) explode('.', $travelStartDate)[1],
            'year' => (int) explode('.', $travelStartDate)[2],
        ];

        if ($birth['month'] > $travel['month']
            || $birth['month'] == $travel['month'] && $birth['day'] > $travel['day']) {
            return $travel['year'] - $birth['year'] - 1;
        }
        else {
            return $travel['year'] - $birth['year'];
        }
    }

    /**
     * вычисление суммы после скидки на ребёнка
     * @param int $age
     * @param int $summa
     * @return float
     */
    public function calcAgeSum(int $age, int $summa): float
    {
        $newSum = $summa;
        switch (true) {
            case $age < 18 && $age >= 12:
                $newSum *= 0.9;
                break;
            case $age < 12 && $age >= 6:
                $newSum = $newSum * 0.3 < 4500 ? $newSum * 0.7 : $newSum - 4500;
                break;
            case $age < 6 && $age >= 3:
                $newSum *= 0.2;
                break;
            case $age < 3 && $age >= 0:
                $newSum = 0;
                break;
        }
        return $newSum;
    }
    /**
     * вычисление размера скидки за раннее бронирование
     * @param string $paymentDate
     * @param string $travelStartDate
     * @return int
     */
    public function calcBooking(string $paymentDate, string $travelStartDate): int
    {
        $payment = [
            'day' => (int) explode('.', $paymentDate)[0],
            'month' => (int) explode('.', $paymentDate)[1],
            'year' => (int) explode('.', $paymentDate)[2],
        ];

        $travel = [
            'day' => (int) explode('.', $travelStartDate)[0],
            'month' => (int) explode('.', $travelStartDate)[1],
            'year' => (int) explode('.', $travelStartDate)[2],
        ];

        $now = [
            'day' => (int) date('d'),
            'month' => (int) date('m'),
            'year' => (int) date('Y'),
        ];

        $travelRange = 2;
        switch (true) {
            case ($travel['year'] > $now['year']
                && (
                    (
                        ($travel['month'] === 4 || $travel['month'] === 6 || $travel['month'] === 9)
                        && ($travel['day'] >= 1 && $travel['day'] <= 30)
                    )
                    || (
                        ($travel['month'] === 5 || $travel['month'] === 7 || $travel['month'] === 8)
                        && ($travel['day'] >= 1 && $travel['day'] <= 31)
                        )
                    )
                ) :
                $travelRange = 1;
                break;
            case $travel['year'] > $now['year']
                && (
                    (
                        $travel['month'] === 1
                        && ($travel['day'] >= 15 && $travel['day'] <= 31)
                    )
                    || (
                        $travel['month'] === 3
                        && ($travel['day'] >= 1 && $travel['day'] <= 31)
                    )
                    || (
                        $travel['month'] === 2
                        && ($travel['day'] >= 1 && $travel['day'] <= 28)
                        )
                    || (
                        ($travel['year'] % 4 === 0 && $travel['year'] % 100 !== 0)
                        && $travel['month'] === 2
                        && ($travel['day'] >= 1 && $travel['day'] <= 29)
                        )
                ) :
                $travelRange = 3;
                break;
        }

        $bookingRange = 0;

        switch (true) {
            case ($travelRange === 1
                && $payment['year'] === $now['year']
                && $payment['month'] <= 11)
                || ($travelRange === 2
                    && $payment['year'] === $now['year']
                    && $payment['month'] <= 3)
                || ($travelRange === 3
                    && $payment['year'] === $now['year']
                    && $payment['month'] <= 8)
            :
                $bookingRange = 7;
                break;
            case ($travelRange === 1
                    && $payment['year'] === $now['year']
                    && $payment['month'] === 12)
                || ($travelRange === 2
                    && $payment['year'] === $now['year']
                    && $payment['month'] === 4)
                || ($travelRange === 3
                    && $payment['year'] === $now['year']
                    && $payment['month'] === 9)
            :
                $bookingRange = 5;
                break;
            case ($travelRange === 1
                    && $payment['year'] === $now['year'] + 1
                    && $payment['month'] === 1
                    && $payment['day'] >= 1 && $payment['day'] <= 31
                    )
                || ($travelRange === 2
                    && $payment['year'] === $now['year']
                    && $payment['month'] === 5)
                || ($travelRange === 3
                    && $payment['year'] === $now['year']
                    && $payment['month'] === 10)
            :
                $bookingRange = 3;
                break;
        }

        return $bookingRange;
    }
}
