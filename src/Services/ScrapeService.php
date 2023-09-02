<?php

namespace App\Services;


use App\Exceptions\ScrapeException;
use Predis\Client;
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

    /**
     * Check for index API, if already paginated data for a given range called before, such page=1, pageSize=10
     * So, that for similar data no need to call db query
     *
     * @param CacheService $client
     * @param int $page
     * @param int $pageSize
     * @return bool
     */
    public function checkCache(CacheService $client, int $page, int $pageSize): bool
    {
        return $page == $client->getData('page') && $pageSize == $client->getData('perPage');
    }

}
