<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CalcControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $client->xmlHttpRequest(
            'POST',
            '/api/calc',
            [
                'summa' => '10000',
                'age' => '1.1.2000',
                'travelStartDate' => '1.5.2025',
                'paymentDate' => '10.11.2024',
                ]
        );
        $client->catchExceptions(false);
        $response = $client->getResponse();

        $this->assertEquals($response->getStatusCode(), 200);
        $expected = json_encode([
            'status' => 200,
            'body' => [
                'summa' => 9300,
                ],
            ]);
        $this->assertEquals($response->getContent(), $expected);

    }
}
