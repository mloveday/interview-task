<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Response\ErrorMessageService;
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
        $books = $this->bookRepository->findAll();
        return $this->responseService->getResponse($books);
    }

    /** @Route("/books/{id}", name="books_get_one", methods={"GET"}, requirements={"id"="\d+"}) */
    public function getOne(int $id) {
        $book = $this->bookRepository->getOneById($id);
        if (is_null($book)) {
            return $this->responseService->getErrorResponse([ErrorMessageService::bookNotFound($id)], Response::HTTP_NOT_FOUND);
        }
        return $this->responseService->getResponse($book);
    }

    /** @Route("/books", name="books_post", methods={"POST", "PATCH"}) */
    public function post(Request $request) {
        $decodedBody = json_decode($request->getContent());
        if (!isset($decodedBody->id) || is_null($decodedBody->id)) {
            return $this->responseService->getErrorResponse(['id must be supplied and not null'], Response::HTTP_BAD_REQUEST);
        }

        $existingBook = $this->bookRepository->findOneBy(['id' => $decodedBody->id]);
        if (is_null($existingBook)) {
            return $this->responseService->getErrorResponse([ErrorMessageService::bookNotFound($decodedBody->id)], Response::HTTP_NOT_FOUND);
        }
        /** @var Book $book */
        $book = $this->requestService->getUpdatedObject($request->getContent(), Book::class, $existingBook);
        $this->persistenceService->persist($book);
        return $this->responseService->getResponse($book);
    }

    /** @Route("/books", name="books_put", methods={"PUT"}) */
    public function put(Request $request) {
        $errorMessages = $this->checkFields($request, ['author', 'title']);
        if (count($errorMessages) > 0) {
            return $this->responseService->getErrorResponse($errorMessages, Response::HTTP_BAD_REQUEST);
        }

        /** @var Book $book */
        $book = $this->requestService->getDeserializedRequest($request->getContent(), Book::class);
        $this->persistenceService->persist($book);
        return $this->responseService->getResponse($book);
    }

    /** @Route("/books/{id}", name="books_delete", methods={"DELETE"}, requirements={"id"="\d+"}) */
    public function delete(int $id) {
        $book = $this->bookRepository->getOneById($id);
        if (is_null($book)) {
            return $this->responseService->getErrorResponse([ErrorMessageService::bookNotFound($id)], Response::HTTP_NOT_FOUND);
        }
        try {
            $this->persistenceService->delete($book);
        } catch (Exception $e) {
            return $this->responseService->getErrorResponse(["Could not delete book with id $id"], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $this->responseService->getResponse(null);
    }

    private function checkFields(Request $request, array $fields) {
        $errors = [];
        $decodedBody = json_decode($request->getContent());
        if (isset($decodedBody->id)) {
            $errors[] = 'id must not be supplied when creating a new book';
        }
        foreach ($fields as $field) {
            if (!isset($decodedBody->{$field}) || is_null($decodedBody->{$field})) {
                $errors[] = "$field must be supplied and not null";
            }
        }
        return $errors;
    }
}