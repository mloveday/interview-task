<?php

namespace App\Tests\DataFixtures;

use App\DataFixtures\AppFixtures;
use App\DataFixtures\DataFixtureTest;
use App\Entity\ApiKey;
use App\Entity\Book;
use App\Response\ErrorMessageService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ApiKeyTest extends DataFixtureTest {
    public function test_apiKeyCorrect() {
        // given

        // when
        $this->makeRequestWithKey(AppFixtures::API_KEY);
        $response = json_decode($this->client->getResponse()->getContent());

        // then
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertInternalType('array', $response);
    }

    public function test_apiKeyInvalid() {
        // given
        $this->expectException(BadRequestHttpException::class);

        // when
        $this->makeRequestWithKey('invalid_api_key');
        $response = json_decode($this->client->getResponse()->getContent());

        // then
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertNotNull($response->errors);
        $this->assertContains(ErrorMessageService::apiKeyInvalid(), $response->errors);
    }

    public function test_apiKeyMissing() {
        // given
        $this->expectException(BadRequestHttpException::class);

        // when
        $this->client->request('GET', '/api/books');
        $response = json_decode($this->client->getResponse()->getContent());

        // then
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertNotNull($response->errors);
        $this->assertContains(ErrorMessageService::apiKeyRequired(), $response->errors);
    }

    private function makeRequestWithKey($apiKey) {
        $this->client->request('GET', "/api/books?api_key=$apiKey");
    }
}