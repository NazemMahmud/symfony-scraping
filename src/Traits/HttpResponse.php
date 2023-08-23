<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;

trait HttpResponse
{
    public function error_response(array|string $message, int $statusCode = 404): JsonResponse
    {
        return new JsonResponse(['error' => $message], $statusCode);
    }
}
