<?php

namespace App\Controller;

use http\Client;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Panther\Client as PantherClient;
use Symfony\Component\Routing\Annotation\Route;

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
        $driverPath = '/usr/local/bin/chromedriver'; // Path to the downloaded "chromedriver" binary
        echo getenv('PANTHER_CHROME_DRIVER_BINARY');
        $data =  json_decode($request->getContent(), true);

        $options = [
            'chrome_options' => [
                'binary' => '/usr/bin/google-chrome-stable', // Path to the Chrome binary (if different)
                'args' => ['--disable-dev-shm-usage'],
            ],
            'chrome_driver_binary' => '/usr/local/bin/chromedriver', // Explicitly set ChromeDriver binary path
        ];

        // Create the Panther client with the Chrome driver binary path
//        $client = PantherClient::createChromeClient();
        $client = PantherClient::createChromeClient(null, $options);


        $crawler = $client->request('GET', 'https://rekvizitai.vz.lt/en/company-search/');
        $crawler = $this->handleCloudflareVerification($crawler);
        dd($crawler);


//        $driver = static::createPantherClient();
//        $crawler = $driver->request('GET', 'https://rekvizitai.vz.lt/en/company-search/');
//
//         Step 3: Interact with the form and submit it

//        $client->clickLink('Get started');
//        $crawler = $client->waitFor('#formSearch');
//        $form = $crawler->filter('form#formSearch')->form(); // Replace 'your_form_id' with the actual form ID or any other CSS selector
//        $form['code'] = $data['registration_code']; // Replace 'input_field_name' with the name of the form field you want to fill
//        $crawler->submit($form);

        return $this->json([
            'message' => 'Welcome to post controller!',
            'data' => $data,
            "content" => $crawler
        ]);
    }

    private function handleCloudflareVerification($crawler)
    {
        // Check if the cookie permission modal is present
        if ($crawler->filter('#cookiescript_close')->count() > 0) {
            // Close the cookie permission modal
            $this->closeCookiePermissionModal($crawler);
            // Wait for the modal to close
            sleep(3); // Adjust the delay based on your experience with the actual modal
            // Reload the page to bypass the Cloudflare verification
            $crawler = $crawler->reload();
        }
        $htmlContent = $crawler->getContent();
        return $htmlContent;
//        // Check if Cloudflare verification is present
//        if ($crawler->filter('h1:contains("Please complete the security check to access")')->count() > 0) {
////            // Perform actions to pass the verification (e.g., click the checkbox)
////            // ...
////
////            // Reload the page after completing the verification
////            $crawler = $crawler->reload();
////        }
//
//        return $crawler;
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

