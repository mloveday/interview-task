<?php

namespace App\Response;

use App\Entity\Book;

class BookResponse {
    /** @var int */
    public $id;
    /** @var string */
    public $title;
    /** @var string */
    public $author;

    public function __construct(Book $book) {
        $this->id = $book->getId();
        $this->title = $book->getTitle();
        $this->author = $book->getAuthor();
    }
}