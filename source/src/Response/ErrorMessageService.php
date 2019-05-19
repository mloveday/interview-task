<?php

namespace App\Response;

class ErrorMessageService {
    public static function bookNotFound(int $id) {
        return "Book not found with id $id";
    }

    public static function idMustNotBeNull() {
        return 'id must be supplied and not null';
    }

    public static function idMustNotBePresent() {
        return 'id must not be supplied when creating a new book';
    }

    public static function fieldMustBePresent(string $field) {
        return "$field must be supplied and not null";
    }
}