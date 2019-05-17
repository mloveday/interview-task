<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse extends JsonResponse {

    public function __construct($data = null, int $status = self::HTTP_OK, array $headers = [], bool $json = true) {
        parent::__construct($data, $status, $headers, $json);
    }
}