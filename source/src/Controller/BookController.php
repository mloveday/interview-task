<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Service\PersistenceService;
use App\Service\RequestService;
use App\Service\ResponseService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController {
    /** @var BookRepository */
    private $bookRepository;
    /** @var ResponseService */
    private $responseService;
    /** @var RequestService */
    private $requestService;
    /** @var PersistenceService */
    private $persistenceService;

    public function __construct(BookRepository $bookRepository, ResponseService $responseService, RequestService $requestService, PersistenceService $persistenceService) {
        $this->bookRepository = $bookRepository;
        $this->responseService = $responseService;
        $this->requestService = $requestService;
        $this->persistenceService = $persistenceService;
    }

    /** @Route("/books", name="books_get_all", methods={"GET"}) */
    public function getAll() {
        try {
            $books = $this->bookRepository->findAll();
            return $this->responseService->getResponse($books);
        } catch (Exception $e) {
            return $this->responseService->getErrorResponse(["Error getting books"], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /** @Route("/books/{id}", name="books_get_one", methods={"GET"}, requirements={"id"="\d+"}) */
    public function getOne(int $id) {
        try {
            $book = $this->bookRepository->findOneBy(["id" => $id]);
            if (is_null($book)) {
                return $this->responseService->getErrorResponse(["Book not found with id $id"], Response::HTTP_NOT_FOUND);
            }
            return $this->responseService->getResponse($book);
        } catch (Exception $e) {
            return $this->responseService->getErrorResponse([$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /** @Route("/books", name="books_post", methods={"POST", "PATCH"}) */
    public function post(Request $request) {
        $decodedBody = json_decode($request->getContent());
        if (!isset($decodedBody->id) || is_null($decodedBody->id)) {
            return $this->responseService->getErrorResponse(['id must be supplied and not null'], Response::HTTP_BAD_REQUEST);
        }

        $existingBook = $this->bookRepository->findOneBy(['id' => $decodedBody->id]);
        if (is_null($existingBook)) {
            return $this->responseService->getErrorResponse(["Book not found with id $decodedBody->id"], Response::HTTP_NOT_FOUND);
        }
        /** @var Book $book */
        $book = $this->requestService->getUpdatedObject($request->getContent(), Book::class, $existingBook);

        try {
            $this->persistenceService->persist($book);
        } catch (Exception $e) {
            return $this->responseService->getErrorResponse([$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->responseService->getResponse($book);
    }

    /** @Route("/books", name="books_put", methods={"PUT"}) */
    public function put(Request $request) {
        $errorMessages = [];
        $decodedBody = json_decode($request->getContent());
        if (isset($decodedBody->id)) {
            $errorMessages[] = 'id must not be supplied when creating a new book';
        }
        $errorMessages = array_merge($errorMessages, $this->checkFields($decodedBody, ['author', 'title']));
        if (count($errorMessages) > 0) {
            return $this->responseService->getErrorResponse($errorMessages, Response::HTTP_BAD_REQUEST);
        }

        /** @var Book $book */
        $book = $this->requestService->getDeserializedRequest($request->getContent(), Book::class);

        try {
            $this->persistenceService->persist($book);
        } catch (Exception $e) {
            return $this->responseService->getErrorResponse([$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->responseService->getResponse($book);
    }

    /** @Route("/books/{id}", name="books_delete", methods={"DELETE"}, requirements={"id"="\d+"}) */
    public function delete(int $id) {
        try {
            $book = $this->bookRepository->findOneBy(["id" => $id]);
            if (is_null($book)) {
                return $this->responseService->getErrorResponse(["Book not found with id $id"], Response::HTTP_NOT_FOUND);
            }
            // TODO delete book
            try {
                $this->persistenceService->delete($book);
            } catch (Exception $e) {
                return $this->responseService->getErrorResponse(["Could not delete book with id $id"], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return $this->responseService->getResponse(null);
        } catch (Exception $e) {
            return $this->responseService->getErrorResponse([$e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function checkFields($object, array $fields) {
        $errors = [];
        foreach ($fields as $field) {
            if (!isset($object->{$field}) || is_null($object->{$field})) {
                $errorMessages[] = "$field must be supplied and not null";
            }
        }
        return $errors;
    }
}