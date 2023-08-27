<?php

namespace App\Services;


use App\Exceptions\ScrapeException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;



class ScrapeService
{
    private const SCRAPPER_FILE = 'scripts/info-crapper.js';

    /**
     * @param string $registrationCode
     * @throws  BadRequestHttpException|ScrapeException
     * @return array|object
     */
    public function scrapeCompanyInfo(string $registrationCode): array|object
    {
        if (empty($registrationCode)) {
            throw new BadRequestHttpException('Registration code should not be empty');
        }

        $escapedRegistrationCode = escapeshellarg($registrationCode);
        $puppeteerScriptPath = __DIR__ . '/../../' . self::SCRAPPER_FILE;
        exec("node $puppeteerScriptPath $escapedRegistrationCode 2>&1", $output, $returnCode);

        $output = implode("\n", $output);

        if ($returnCode !== 0) {
            throw new ScrapeException(message: $output ?: 'An error occurred while running Puppeteer script.');
        }

        $result = json_decode($output, true);
        if ($result !== null) {
            return $result;
        }

        throw new ScrapeException(message:  $output ?: 'Data not found.');

    }

}
