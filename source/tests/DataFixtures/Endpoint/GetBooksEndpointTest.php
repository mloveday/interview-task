<?php

namespace App\Tests\DataFixtures\Endpoint;

use App\DataFixtures\DataFixtureTest;
use App\Entity\Book;

class GetBooksEndpointTest extends DataFixtureTest {
    public function test_getsListOfAllBooks() {
        // given
        /** @var Book[] $allBooks */
        $allBooks = $this->entityManager->getRepository(Book::class)->findAll();

        // when
        $this->client->request('GET', '/books');
        $responseObject = json_decode($this->client->getResponse()->getContent());

        // then
        $this->assertSameSize($allBooks, $responseObject);
        foreach($allBooks as $book) {
            $foundBooks = array_filter($responseObject, function($object) use ($book) { return $object->id === $book->getId();});
            $this->assertCount(1, $foundBooks, "Expected exactly 1 book to match id {$book->getId()}, found " . count($foundBooks));
            $foundBook = current($foundBooks);
            $this->assertEquals($book->getTitle(), $foundBook->title, "Title mismatch for book with id {$book->getId()}");
            $this->assertEquals($book->getAuthor(), $foundBook->author, "Author mismatch for book with id {$book->getId()}");
        }
    }
}