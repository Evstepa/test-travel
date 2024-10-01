<?php

namespace App\Controller\Api;

use App\Service\Calculation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class CalcController extends AbstractController
{
    #[Route('/api/calc', name: 'api_calc')]
    public function index(Request $request, Calculation $calculation): JsonResponse
    {
        $basedSum = !empty($request->get('summa'))
            ? $request->get('summa')
            : null;
        $participantBirth = !empty($request->get('age'))
            ? date_create($request->get('age'))->format('j.n.Y')
            : null;
        $travelStartDate = !empty($request->get('travelStartDate'))
            ? date_create($request->get('travelStartDate'))->format('j.n.Y')
            : date_create()->format('j.n.Y');
        $paymentDate = !empty($request->get('paymentDate'))
            ? date_create($request->get('paymentDate'))->format('j.n.Y')
            : null;

        if (is_null($basedSum) || is_null($participantBirth)) {
            return $this->json([
                'status' => 400,
                'body' => [
                    'error' => 'Не все данные введены!',
                ],
            ]);
        }

        return $this->json($calculation->calcDiscount(
            $basedSum,
            $participantBirth,
            $travelStartDate,
            $paymentDate,
        ));
    }
}
