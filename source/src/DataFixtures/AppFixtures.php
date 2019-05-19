<?php

namespace App\DataFixtures;

use App\Entity\ApiKey;
use App\Entity\Book;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures implements FixtureInterface {
    public const BOOKS = [
        ['title' => 'Use of Weapons', 'author' => 'Iain M Banks'],
        ['title' => 'Cat\'s Cradle', 'author' => 'Kurt Vonnegut'],
    ];

    public const API_KEY = 'test_api_key';

    public function load(ObjectManager $manager) {
        foreach (self::BOOKS as $bookFixture) {
            $book = new Book();
            $book->setTitle($bookFixture['title'])
                ->setAuthor($bookFixture['author']);
            $manager->persist($book);
        }

        $apiKey = new ApiKey();
        $apiKey->setApiKey(self::API_KEY);
        $manager->persist($apiKey);

        $manager->flush();
    }
}