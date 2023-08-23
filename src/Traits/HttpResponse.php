<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\JsonResponse;

trait HttpResponse
{
    public function error_response(array|string $message, int $statusCode = 404): JsonResponse
    {
        return new JsonResponse(
            [
                'success' => false,
                'message' => $message
            ],
            $statusCode);
    }

    public function success_response(array $response, int $statusCode = 200): JsonResponse
    {
        return new JsonResponse(
            array_merge( ['success' => true], $response),
            $statusCode
        );
    }
}
