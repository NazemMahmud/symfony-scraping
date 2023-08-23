<?php

namespace App\Services;

//use Exception;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;



class ScrapeService
{
    /**
     * @throws BadRequestHttpException
     */
    public function scrapeCompanyInfo(string $registrationCode)
    {
        if (empty($registrationCode)) {
            throw new BadRequestHttpException('Registration code should not be empty');
        }

    }

}
