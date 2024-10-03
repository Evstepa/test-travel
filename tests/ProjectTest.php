<?php

namespace Tests;

use App\Service\Calculation;
use PHPUnit\Framework\TestCase;

class ProjectTest extends TestCase
{
    public const DATES = [
        [
            'travel' => '2.4.2025',
            'payment' => '10.10.2024',
            'discount' => 7,
        ],
        [
            'travel' => '12.6.2025',
            'payment' => '10.12.2024',
            'discount' => 5,
        ],
        [
            'travel' => '20.8.2025',
            'payment' => '10.1.2025',
            'discount' => 3,
        ],
        [
            'travel' => '2.9.2025',
            'payment' => '10.2.2025',
            'discount' => 0,
        ],
        [
            'travel' => '2.10.2024',
            'payment' => '10.3.2024',
            'discount' => 7,
        ],
        [
            'travel' => '2.11.2024',
            'payment' => '10.4.2024',
            'discount' => 5,
        ],
        [
            'travel' => '2.12.2024',
            'payment' => '10.5.2024',
            'discount' => 3,
        ],
        [
            'travel' => '2.1.2025',
            'payment' => '10.6.2024',
            'discount' => 0,
        ],
        [
            'travel' => '20.1.2025',
            'payment' => '10.8.2024',
            'discount' => 7,
        ],
        [
            'travel' => '20.2.2025',
            'payment' => '10.9.2024',
            'discount' => 5,
        ],
        [
            'travel' => '20.3.2025',
            'payment' => '10.10.2024',
            'discount' => 3,
        ],
        [
            'travel' => '30.3.2025',
            'payment' => '10.11.2024',
            'discount' => 0,
        ],
    ];

    public const AGE_SUM = [
        [
            'age' => 2,
            'oldSum' => 10000,
            'newSum' => 0,
        ],
        [
            'age' => 4,
            'oldSum' => 10000,
            'newSum' => 2000,
        ],
        [
            'age' => 7,
            'oldSum' => 10000,
            'newSum' => 7000,
        ],
        [
            'age' => 7,
            'oldSum' => 100000,
            'newSum' => 95500,
        ],
        [
            'age' => 13,
            'oldSum' => 10000,
            'newSum' => 9000,
        ],
        [
            'age' => 19,
            'oldSum' => 10000,
            'newSum' => 10000,
        ],
    ];

    /**
     * проверка функции вычисления возраста
     * @return void
     */
    public function testCalcAge()
    {
        $calc = new Calculation();
        $n = random_int(1, 50);

        $birthDate = date_create()->modify("-{$n} years")->format('j.n.Y');
        $travelStartDate = date_create()->modify("+{$n} years")->format('j.n.Y');

        $actual = $calc->calcAge($birthDate, $travelStartDate);
        $expected = $n * 2;

        $this->assertEquals($expected, $actual);
    }

    /**
     * проверка функции вычисления скидки за раннее бронирование
     * @return void
     */
    public function testCalcBooking()
    {
        $calc = new Calculation();

        foreach ($this::DATES as $key => $value) {
            $actual = $calc->calcBooking($value['payment'], $value['travel']);
            $expected = $value['discount'];

            $this->assertEquals($expected, $actual);
        }
    }

    /**
     * проверка функции вычисления скидки ребёнку
     * @return void
     */
    public function testCalcAgeDiscount()
    {
        $calc = new Calculation();

        foreach ($this::AGE_SUM as $key => $value) {
            $actual = $calc->calcAgeSum($value['age'], $value['oldSum']);
            $expected = $value['newSum'];

            $this->assertEquals($expected, $actual);
        }
    }

    /**
     * проверка основной функции
     * @return void
     */
    public function testCalcDiscount()
    {
        $calc = new Calculation();
        foreach ($this::AGE_SUM as $key => $value) {
            $summaChild = $calc->calcAgeSum($value['age'], $value['oldSum']);

            foreach ($this::DATES as $key => $item) {
                $discount = $calc->calcBooking($item['payment'], $item['travel']);

                $actual = ($summaChild * $discount / 100) < 1500
                    ? $summaChild * (1 - $discount / 100)
                    : $summaChild - 1500;

                $expected = ($value['newSum'] * $item['discount'] / 100) < 1500
                    ? $value['newSum'] * (1 - $item['discount'] / 100)
                    : $value['newSum'] - 1500;

                $this->assertEquals($expected, $actual);
            }
        }
    }
}
