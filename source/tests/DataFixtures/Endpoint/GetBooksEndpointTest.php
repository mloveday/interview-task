<?php

namespace App\Tests\DataFixtures\Endpoint;

use App\DataFixtures\DataFixtureTest;
use App\DataFixtures\AppFixtures;
use App\Entity\Book;

class GetBooksEndpointTest extends DataFixtureTest {
    public function test_getsListOfAllBooks() {
        // given
        $allBooks = $this->entityManager->getRepository(Book::class)->findAll();

        // when
        $this->client->request('GET', '/books');
        $responseObject = json_decode($this->client->getResponse()->getContent());

        // then
        $this->assertSameSize($allBooks, $responseObject);
        $this->assertSameSize(AppFixtures::BOOKS, $responseObject);
    }
}