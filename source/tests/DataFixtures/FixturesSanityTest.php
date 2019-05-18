<?php

namespace App\Tests\DataFixtures;

use App\DataFixtures\AppFixtures;
use App\DataFixtures\DataFixtureTest;
use App\Entity\Book;

class FixturesSanityTest extends DataFixtureTest {
    public function test_fixturesAreLoaded() {
        // given

        // when
        $allBooks = $this->entityManager->getRepository(Book::class)->findAll();

        // then
        $this->assertSameSize(AppFixtures::BOOKS, $allBooks);
    }
}