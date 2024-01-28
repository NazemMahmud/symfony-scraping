<?php

namespace Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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

    /**
     * Company update: will get a conflict because of registration code
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
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

        } catch (ClientException $ex) {
            $statusCode = $ex->getResponse()->getStatusCode();
            $this->assertSame(409, $statusCode);

            $responseBody = json_decode($ex->getResponse()->getBody()->getContents(), true);
            $this->assertFalse($responseBody['success']);
            $this->assertSame('The registration code is already in use.', $responseBody['message']);
            $this->assertSame(409, $statusCode);
        }
    }

    /**
     * Company update: validation error because of required fields missing
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testUpdateCompanyValidationError(): void
    {
        $id = 1;
        $requestData = [
            'registration_code' => '302801468',
        ];

        try {
            $response = $this->client->request('PUT', '/api/company/' . $id, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
                'json' => $requestData,
            ]);
            $statusCode = $response->getStatusCode();
            $this->assertSame(200, $statusCode);

        } catch (ClientException $ex) {
            $statusCode = $ex->getResponse()->getStatusCode();
            $this->assertSame(403, $statusCode);

            $responseBody = json_decode($ex->getResponse()->getBody()->getContents(), true);
            $this->assertFalse($responseBody['success']);
            $this->assertIsArray($responseBody['message']);

            $expectedString = 'The vat cannot be blank.';
            $this->assertContains($expectedString, $responseBody['message']);
        }
    }


    public function testUpdateCompanySuccess(): void
    {
        $id = 1;
        $requestData = [
            'registration_code' => '992801468',
            "vat" => "987654321",
            "name" => "ABCD Company",
            "address" => "Test Address",
            "mobile_phone" => "https://rekvizitai.vz.lt/timages/%3DHGZ1VQAmVQV1NPZ3ZmX.gif"
        ];

        $response = $this->client->request('PUT', '/api/company/' . $id, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'json' => $requestData,
        ]);
        $statusCode = $response->getStatusCode();
        $responseBody = json_decode($response->getBody()->getContents(), true);

        $this->assertSame(200, $statusCode);
        $this->assertTrue($responseBody['success']);
        $this->assertEquals('Company updated', $responseBody['message']);
    }

    /**
     * Purpose of this to return a test failure, this should give a company not found error
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testDeleteCompanyFailResponse(): void
    {
        $id = 100;

        $response = $this->client->request('DELETE', '/api/company/' . $id);
        $statusCode = $response->getStatusCode();
        $responseBody = json_decode($response->getBody()->getContents(), true);

        $this->assertSame(200, $statusCode);
        $this->assertTrue($responseBody['success']);
        $this->assertEquals('Company successfully deleted', $responseBody['message']);
    }


    public function testDeleteCompanySuccess(): void
    {
        $id = 10;

        $response = $this->client->request('DELETE', '/api/company/' . $id);
        $statusCode = $response->getStatusCode();
        $responseBody = json_decode($response->getBody()->getContents(), true);

        $this->assertSame(200, $statusCode);
        $this->assertTrue($responseBody['success']);
        $this->assertEquals('Company successfully deleted', $responseBody['message']);
    }

    /**
     * Already deleted data in above test, so this one should give a 400 error
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testDeleteAlreadyNotFound(): void
    {
        $id = 10;
        try {
            $response = $this->client->request('DELETE', '/api/company/' . $id);
        } catch (ClientException $ex) {
            $statusCode = $ex->getResponse()->getStatusCode();
            $this->assertSame(400, $statusCode);

            $responseBody = json_decode($ex->getResponse()->getBody()->getContents(), true);
            $this->assertFalse($responseBody['success']);
            $this->assertEquals('Company not found', $responseBody['message']);
        }
    }
}
