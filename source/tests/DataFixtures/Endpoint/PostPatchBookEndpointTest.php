<?php

namespace App\Tests\DataFixtures\Endpoint;

use App\DataFixtures\DataFixtureTest;
use App\Entity\Book;
use App\Response\ErrorMessageService;
use Symfony\Component\HttpFoundation\Response;

class PostPatchBookEndpointTest extends DataFixtureTest {
    const URL = '/books';

    public function test_postUpdatesBook() {
        $this->methodUpdatesBook('POST');
    }

    public function test_patchUpdatesBook() {
        $this->methodUpdatesBook('PATCH');
    }

    private function methodUpdatesBook(string $method) {
        // given
        /** @var Book[] $existingBooks */
        $existingBooks = $this->entityManager->getRepository(Book::class)->findAll();
        $existingBook = current($existingBooks);
        $updatedBook = (object) [
            'id' => $existingBook->getId(),
            'author' => 'Philip K Dick',
            'title' => 'The Three Stigmata of Palmer Eldritch',
        ];

        // when
        $this->makeRequest($method, $updatedBook);
        $responseObject = json_decode($this->client->getResponse()->getContent());

        // then
        $this->assertEquals($updatedBook->id, $responseObject->id, "id mismatch for book with id {$existingBook->getId()}");
        $this->assertEquals($updatedBook->title, $responseObject->title, "Title mismatch for book with id {$existingBook->getId()}");
        $this->assertEquals($updatedBook->author, $responseObject->author, "Author mismatch for book with id {$existingBook->getId()}");
    }

    public function test_postReturnsErrorWhenNoBookFound() {
        $this->methodReturnsErrorWhenNoBookFound('POST');
    }

    public function test_patchReturnsErrorWhenNoBookFound() {
        $this->methodReturnsErrorWhenNoBookFound('PATCH');
    }

    private function methodReturnsErrorWhenNoBookFound(string $method) {
        // given
        /** @var Book[] $allBooks */
        $allBooks = $this->entityManager->getRepository(Book::class)->findAll();
        /** @var int $maxId */
        $maxId = array_reduce(
            $allBooks,
            function (int $prev, Book $book) {
                if ($book->getId() > $prev) {
                    return $book->getId();
                }
                return $prev;
            },
            0);
        $bookIdDoesNotExist = $maxId + 1;

        $updatedBook = (object) [
            'id' => $bookIdDoesNotExist,
            'author' => 'Philip K Dick',
            'title' => 'The Three Stigmata of Palmer Eldritch',
        ];

        // when
        $this->makeRequest($method, $updatedBook);
        $responseObject = json_decode($this->client->getResponse()->getContent());

        // then
        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertContains(ErrorMessageService::bookNotFound($bookIdDoesNotExist), $responseObject->errors);
    }

    public function test_postReturnsErrorWhenNoIdInRequest() {
        $this->methodReturnsErrorWhenNoIdInRequest('POST');
    }

    public function test_patchReturnsErrorWhenNoIdInRequest() {
        $this->methodReturnsErrorWhenNoIdInRequest('PATCH');
    }

    private function methodReturnsErrorWhenNoIdInRequest(string $method) {
        // given
        $updatedBook = (object) [
            'author' => 'Philip K Dick',
            'title' => 'The Three Stigmata of Palmer Eldritch',
        ];

        // when
        $this->makeRequest($method, $updatedBook);
        $responseObject = json_decode($this->client->getResponse()->getContent());

        // then
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertContains(ErrorMessageService::idMustNotBeNull(), $responseObject->errors);
    }

    private function makeRequest(string $method, $content) {
        $this->client->request($method, self::URL, [],[], [], json_encode($content));
    }
}