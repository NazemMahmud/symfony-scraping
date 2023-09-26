<?php

namespace Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

// Todo: unit test
class CompanyControllerTest extends WebTestCase
{

    protected $baseUrl;
    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->baseUrl = getenv('TEST_BASE_URL');
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
        ]);
    }

    public function testUpdateCompanyConflict(): void
    {

        $id = 1;
        $requestData = [
            'registration_code' => '302801468',
            "vat" => "987654321",
            "name" => "ABCD Company",
            "address" => "Test Address",
            "mobile_phone" => "https://rekvizitai.vz.lt/timages/%3DHGZ1VQAmVQV1NPZ3ZmX.gif"
        ];

        try {

            $response = $this->client->request('PUT', '/api/company/' . $id, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => $requestData,
            ]);
            $statusCode = $response->getStatusCode();
            $responseBody = $response->getBody()->getContents();

//        $requestUrl = $client->getRequest()->getUri();
//        $response = $client->getResponse();
//        dump($client->getResponse());

            $this->assertSame(200, $statusCode);

        } catch (ClientException $ex) {
            $statusCode = $ex->getResponse()->getStatusCode();

            // You can use the status code for assertions or other purposes
            $this->assertSame(409, $statusCode);

            // Display the response body for 409 error
            $responseBody = json_decode($ex->getResponse()->getBody()->getContents(), true);
            $this->assertFalse($responseBody['success']);
            $this->assertSame('The registration code is already in use.', $responseBody['message']);
            $this->assertSame(409, $statusCode);
        }
    }
}
