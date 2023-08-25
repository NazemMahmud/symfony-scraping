<?php

namespace App\Exceptions;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;


class ScrapeException extends HttpException
{

    public function __construct(
        $statusCode = Response::HTTP_NOT_FOUND,
        $message = 'Something went wrong with scraping',
        \Throwable $previous = null,
        array $headers = [],
        $code = 0)
    {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
    }
}
