<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ScrapeController extends AbstractController
{
    #[Route('/api/scrape', name: 'app_scrape')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
        ]);
    }

    #[Route('/api/scrape/new', name: 'app_scrape_store', methods: ['POST'])]
    public function store(Request $request): JsonResponse
    {
        $data =  json_decode($request->getContent(), true);

        return $this->json([
            'message' => 'Welcome to post controller!',
            'data' => $data
        ]);
    }
}
