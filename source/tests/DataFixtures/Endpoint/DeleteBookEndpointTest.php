<?php

namespace App\Tests\DataFixtures\Endpoint;

use App\DataFixtures\AppFixtures;
use App\DataFixtures\DataFixtureTest;
use App\Entity\Book;
use App\Response\ErrorMessageService;
use Symfony\Component\HttpFoundation\Response;

class DeleteBookEndpointTest extends DataFixtureTest {
    public function test_deleteDeletesBook() {
        // given
        /** @var Book[] $existingBooks */
        $existingBooks = $this->entityManager->getRepository(Book::class)->findAll();
        $existingBook = current($existingBooks);

        // when
        $this->makeRequest($existingBook->getId());

        // then
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        /** @var Book $persistedBook */
        $persistedBook = $this->entityManager->getRepository(Book::class)->findOneBy(['id' => $existingBook->getId()]);
        $this->assertNull($persistedBook);
    }

    public function test_returnsErrorWhenNoBookFound() {
        // given
        /** @var Book[] $existingBooks */
        $existingBooks = $this->entityManager->getRepository(Book::class)->findAll();
        /** @var int $maxId */
        $maxId = array_reduce(
            $existingBooks,
            function (int $prev, Book $book) {
                if ($book->getId() > $prev) {
                    return $book->getId();
                }
                return $prev;
            },
            0);
        $bookIdDoesNotExist = $maxId + 1;

        // when
        $this->makeRequest($bookIdDoesNotExist);
        $responseObject = json_decode($this->client->getResponse()->getContent());

        // then
        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertContains(ErrorMessageService::bookNotFound($bookIdDoesNotExist), $responseObject->errors);

        /** @var Book[] $allBooks */
        $allBooks = $this->entityManager->getRepository(Book::class)->findAll();
        $this->assertEquals($existingBooks, $allBooks);

    }

    private function makeRequest(int $id) {
        $this->client->request('DELETE', "/api/books/$id?api_key=".AppFixtures::API_KEY);
    }
}