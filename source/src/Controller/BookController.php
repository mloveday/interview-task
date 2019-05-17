<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Service\ResponseService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController {
    /** @var BookRepository */
    private $bookRepository;
    /** @var ResponseService */
    private $responseService;

    public function __construct(BookRepository $bookRepository, ResponseService $responseService) {
        $this->bookRepository = $bookRepository;
        $this->responseService = $responseService;
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

    /** @Route("/books/{id}", name="books_get_one", methods={"GET"}) */
    public function getOne($id) {
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

    /** @Route("/books", name="books_post", methods={"POST"}) */
    public function post() {
        return $this->responseService->getErrorResponse(["Endpoint not implemented"], Response::HTTP_NOT_FOUND);
    }

    /** @Route("/books", name="books_put", methods={"PUT"}) */
    public function put() {
        return $this->responseService->getErrorResponse(["Endpoint not implemented"], Response::HTTP_NOT_FOUND);
    }

    /** @Route("/books", name="books_patch", methods={"PATCH"}) */
    public function patch() {
        return $this->responseService->getErrorResponse(["Endpoint not implemented"], Response::HTTP_NOT_FOUND);
    }

    /** @Route("/books", name="books_delete", methods={"DELETE"}) */
    public function delete() {
        return $this->responseService->getErrorResponse(["Endpoint not implemented"], Response::HTTP_NOT_FOUND);
    }
}