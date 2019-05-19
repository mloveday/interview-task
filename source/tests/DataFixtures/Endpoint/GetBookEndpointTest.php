<?php

namespace App\Tests\DataFixtures\Endpoint;

use App\DataFixtures\AppFixtures;
use App\DataFixtures\DataFixtureTest;
use App\Entity\Book;
use App\Response\ErrorMessageService;
use Symfony\Component\HttpFoundation\Response;

class GetBookEndpointTest extends DataFixtureTest {
    public function test_getsBookWithId() {
        // given
        /** @var Book[] $allBooks */
        $allBooks = $this->entityManager->getRepository(Book::class)->findAll();
        $book = current($allBooks);

        // when
        $this->client->request('GET', $this->urlForBookWithId($book->getId()));
        $responseObject = json_decode($this->client->getResponse()->getContent());

        // then
        $this->assertEquals($book->getId(), $responseObject->id, "id mismatch for book with id {$book->getId()}");
        $this->assertEquals($book->getTitle(), $responseObject->title, "Title mismatch for book with id {$book->getId()}");
        $this->assertEquals($book->getAuthor(), $responseObject->author, "Author mismatch for book with id {$book->getId()}");
    }

    public function test_returnsErrorWhenNoBookFound() {
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

        // when
        $this->client->request('GET', $this->urlForBookWithId($bookIdDoesNotExist));
        $responseObject = json_decode($this->client->getResponse()->getContent());

        // then
        $this->assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertContains(ErrorMessageService::bookNotFound($bookIdDoesNotExist), $responseObject->errors);
    }

    private function urlForBookWithId(int $id) {
        return "/api/books/$id?api_key=".AppFixtures::API_KEY;
    }
}