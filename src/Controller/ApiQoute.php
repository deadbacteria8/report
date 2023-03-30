<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class ApiQoute
{
    #[Route("/api/qoute", name:"api")]
    public function jsonNumber(): Response
    {
        $quotes = [
            'The greatest glory in living lies not in never falling, but in rising every time we fall. -Nelson Mandela',
            'The way to get started is to quit talking and begin doing. -Walt Disney',
            'The future belongs to those who believe in the beauty of their dreams. -Eleanor Roosevelt'
        ];

        $randomIndex = rand(0, 2);
        $randomQuote = $quotes[$randomIndex];

        $data = [
            'quote' => $randomQuote,
            'datum' => date('Y-m-d'),
            'tid' => time()
        ];

        return new JsonResponse($data);
    }
}
