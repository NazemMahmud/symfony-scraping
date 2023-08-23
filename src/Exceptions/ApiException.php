<?php

namespace App\Exceptions;

use App\Traits\HttpResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * TODO: for now ignore, if not used then i will remove
 */
class ApiException extends HttpException
{
    use HttpResponse;

    public function __construct($statusCode = 404, $message = null, \Throwable $previous = null, array $headers = [], $code = 0)
    {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
//        $response = new JsonResponse(['error' => $message], $statusCode);
//        var_dump($response);
//        return $response;
//        return $this->error_response($message, $statusCode);
    }
}
