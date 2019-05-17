<?php

namespace App\Service;

use App\Response\ApiResponse;
use App\Response\ErrorResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

class RequestService {
    /** @var SerializerInterface */
    private $serializer;

    public function __construct(SerializerInterface $serializer) {
        $this->serializer = $serializer;
    }

    public function getDeserializedRequest($data, $type) {
        return $this->serializer->deserialize($data, $type, 'json');
    }

    public function getUpdatedObject($data, $type, $existingObject) {
        return $this->serializer->deserialize($data, $type, 'json', ['object_to_populate' => $existingObject]);
    }
}