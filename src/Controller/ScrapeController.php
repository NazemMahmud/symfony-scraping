<?php

namespace App\Controller;

use Facebook\WebDriver\WebDriverBy;
use http\Client;
use http\Exception\RuntimeException;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Panther\Client as PantherClient;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Panther\DomCrawler\Crawler;

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
/*
        // Create the Panther client with the Chrome driver binary path
        $client = PantherClient::createChromeClient();
        $crawler = $client->request('GET', 'https://rekvizitai.vz.lt/en/company-search/');
        $crawler = $this->handleCloudflareVerification($crawler, $client);
        dd($crawler->html());


//        $driver = static::createPantherClient();
//        $crawler = $driver->request('GET', 'https://rekvizitai.vz.lt/en/company-search/');
//
//         Step 3: Interact with the form and submit it

//        $client->clickLink('Get started');
//        $crawler = $client->waitFor('#formSearch');
//        $form = $crawler->filter('form#formSearch')->form(); // Replace 'your_form_id' with the actual form ID or any other CSS selector
//        $form['code'] = $data['registration_code']; // Replace 'input_field_name' with the name of the form field you want to fill
//        $crawler->submit($form);
*/

        // Run the Puppeteer script to capture HTML content
//        dd(__DIR__ . '/../../scripts/cloudflare-bypass.js');
        $puppeteerScriptPath = __DIR__ . '/../../scripts/cloudflare-bypass.js';
        exec("node $puppeteerScriptPath 2>&1", $output, $returnCode);

        if ($returnCode !== 0) {
            return $this->json([
                'message' => 'An error occurred while running Puppeteer script.',
                'code' => $returnCode,
                'output' => $output,
            ], 500);
        }

        // Get the captured HTML content from the output
        $htmlContent = implode("\n", $output);
//        dump('htmlContent: ');
//        dd($htmlContent);

        // You can now use $htmlContent for further processing

        return $this->json([
            'message' => 'Welcome to post controller!',
            'data' => $data,
            'html' => $htmlContent,
//            "content" => $crawler
        ]);
    }

    private function handleCloudflareVerification($crawler, $client)
    {


        if ($crawler->filter('h2:contains("Checking if the site connection is secure")')->count() > 0) {
            $client->waitForVisibility('#turnstile-wrapper iframe');

            dd($crawler->html());
            // Find the iframe containing the verification checkbox using the title attribute
            $iframe = $crawler->filter('iframe[title="Widget containing a Cloudflare security challenge"]')->first();
            if (!$iframe->count()) {
                throw new RuntimeException('Cloudflare iframe not found.');
            }
            $client = $crawler->getClient();
            $client->switchTo()->frame($iframe->attr('id'));

            // Get the iframe id and use it as a dynamic part in the selector
            // Find the verification checkbox inside the iframe using relative CSS selector
            $checkboxLabel = $client->waitFor('.ctp-checkbox-label');
            if (!$checkboxLabel->count()) {
                throw new RuntimeException('Cloudflare verification checkbox not found.');
            }


            $checkbox = $checkboxLabel->filter('input[type="checkbox"]')->first();

            // Check the checkbox to complete the verification
            $checkbox->check();

            // Submit the verification form
            $verificationForm = $crawler->selectButton('Continue')->form();
            $crawler = $crawler->submit($verificationForm);

            // Switch back to the default context (outside the iframe)
            $client->switchTo()->defaultContent();
            ///////

            // Switch to the iframe
//            $client = $crawler->getClient();
//            $client->switchTo()->frame($iframe->attr('id'));
//
//            // Find the verification checkbox using the dynamic selector
//            $checkbox = $client->waitFor($checkboxSelector);
//
//            // Check the checkbox to complete the verification
//            $checkbox->check();
//
//            // Submit the verification form
//            $verificationForm = $crawler->selectButton('Continue')->form();
//            $crawler = $crawler->submit($verificationForm);
        }

        // Check if the cookie permission modal is present
//        if ($crawler->filter('#cookiescript_close')->count() > 0) {
//            // Close the cookie permission modal
//            $this->closeCookiePermissionModal($crawler);
//            // Wait for the modal to close
//            sleep(3); // Adjust the delay based on your experience with the actual modal
//            // Reload the page to bypass the Cloudflare verification
//            $crawler = $crawler->reload();
//        }

//        // Check if Cloudflare verification is present
//        if ($crawler->filter('h1:contains("Please complete the security check to access")')->count() > 0) {
////            // Perform actions to pass the verification (e.g., click the checkbox)
////            // ...
////
////            // Reload the page after completing the verification
////            $crawler = $crawler->reload();
////        }
//
        return $crawler;
    }

    private function closeCookiePermissionModal($crawler)
    {
        $closeButton = $crawler->filter('#cookiescript_close')->first();

        // Check if the close button exists before attempting to click
        if ($closeButton->count() > 0) {
            $closeButton->click();
        }
    }
}

