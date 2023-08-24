<?php

namespace App\Exceptions;

use App\Traits\HttpResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;


class DBException extends HttpException
{
    use HttpResponse;

    public function __construct(
        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR,
        $message = 'Something went wrong while saving data',
        \Throwable $previous = null,
        array $headers = [],
        $code = 0)
    {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }
}
