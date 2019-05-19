<?php

namespace App\Tests\DataFixtures\Endpoint;

use App\DataFixtures\DataFixtureTest;
use App\Entity\Book;

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

    private function urlForBookWithId(int $id) {
        return "/books/$id";
    }
}