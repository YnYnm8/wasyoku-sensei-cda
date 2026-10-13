<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ViewHistoryController extends AbstractController
{
    #[Route('/api/history', name: 'api_history_create', methods: ['POST'])]
    public function create(): JsonResponse
    {
        return $this->json(['status' => 'ok'], 201);
    }
}