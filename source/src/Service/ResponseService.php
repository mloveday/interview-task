<?php

namespace App\Service;

use App\Response\ApiResponse;
use App\Response\ErrorResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class ResponseService {
    /** @var SerializerInterface */
    private $serializer;

    public function __construct(SerializerInterface $serializer) {
        $this->serializer = $serializer;
    }

    public function getResponse($data) {
        $response = $this->serializer->serialize($data, 'json');
        return new ApiResponse($response);
    }

    public function getErrorResponse(array $messages, int $code) {
        return new JsonResponse(new ErrorResponse($messages), $code);
    }
}