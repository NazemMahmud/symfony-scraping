<?php

namespace App\Exceptions;

use App\Traits\HttpResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;


class ScrapeException extends HttpException
{
    use HttpResponse;

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
