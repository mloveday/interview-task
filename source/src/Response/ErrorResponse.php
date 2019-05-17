<?php

namespace App\Response;

class ErrorResponse {
    /** @var string[] */
    public $errors;

    public function __construct(array $errors) {
        $this->errors = $errors;
    }
}