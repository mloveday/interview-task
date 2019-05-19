<?php

namespace App\Response;

class ErrorMessageService {
    public static function bookNotFound(int $id) {
        return "Book not found with id $id";
    }
}