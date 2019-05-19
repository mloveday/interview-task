<?php

namespace App\Tests\DataFixtures\Endpoint;

use App\DataFixtures\DataFixtureTest;
use App\Entity\Book;
use App\Response\ErrorMessageService;
use Symfony\Component\HttpFoundation\Response;

class PutBookEndpointTest extends DataFixtureTest {
    const URL = '/books';

    public function test_putUpdatesBook() {
        // given
        $newBook = (object) [
            'author' => 'Philip K Dick',
            'title' => 'The Three Stigmata of Palmer Eldritch',
        ];

        // when
        $this->makeRequest($newBook);
        $responseObject = json_decode($this->client->getResponse()->getContent());

        // then
        $this->assertNotNull($responseObject->id);
        $this->assertEquals($newBook->title, $responseObject->title, "Title mismatch for new book");
        $this->assertEquals($newBook->author, $responseObject->author, "Author mismatch for new book");

        /** @var Book $persistedBook */
        $persistedBook = $this->entityManager->getRepository(Book::class)->findOneBy(['id' => $responseObject->id]);
        $this->assertNotNull($persistedBook);
        $this->assertEquals($responseObject->id, $persistedBook->getId());
        $this->assertEquals($responseObject->title, $persistedBook->getTitle());
        $this->assertEquals($responseObject->author, $persistedBook->getAuthor());
    }

    public function test_putReturnsErrorWhenTitleNotInRequest() {
        // given
        $newBook = (object) [
            'author' => 'Philip K Dick',
        ];

        // when
        $this->makeRequest($newBook);
        $responseObject = json_decode($this->client->getResponse()->getContent());

        // then
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertContains(ErrorMessageService::fieldMustBePresent('title'), $responseObject->errors);
    }

    public function test_putReturnsErrorWhenAuthorNotInRequest() {
        // given
        $newBook = (object) [
            'title' => 'The Three Stigmata of Palmer Eldritch',
        ];

        // when
        $this->makeRequest($newBook);
        $responseObject = json_decode($this->client->getResponse()->getContent());

        // then
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertContains(ErrorMessageService::fieldMustBePresent('author'), $responseObject->errors);
    }

    public function test_putReturnsErrorWhenIdInRequest() {
        // given
        $updatedBook = (object) [
            'id' => 5,
            'author' => 'Philip K Dick',
            'title' => 'The Three Stigmata of Palmer Eldritch',
        ];

        // when
        $this->makeRequest($updatedBook);
        $responseObject = json_decode($this->client->getResponse()->getContent());

        // then
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertContains(ErrorMessageService::idMustNotBePresent(), $responseObject->errors);
    }

    private function makeRequest($content) {
        $this->client->request('PUT', self::URL, [],[], [], json_encode($content));
    }
}