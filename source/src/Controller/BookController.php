<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Response\BookResponse;
use App\Response\ErrorResponse;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController {

    /** @Route("/books", name="books_get_all", methods={"GET"}) */
    public function getAll(BookRepository $bookRepository) {
        try {
            $books = $bookRepository->findAll();
            $response = array_map(
                function (Book $book) { return new BookResponse($book);},
                $books
            );
            return new JsonResponse($response);
        } catch (Exception $e) {
            return new JsonResponse(new ErrorResponse(["Error getting books"]), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /** @Route("/books/{id}", name="books_get_one", methods={"GET"}) */
    public function getOne(BookRepository $bookRepository, $id) {
        try {
            $book = $bookRepository->findOneBy(["id" => $id]);
            if (is_null($book)) {
                return new JsonResponse(new ErrorResponse(["Book not found with id $id"]), Response::HTTP_NOT_FOUND);
            }
            $response = new BookResponse($book);
            return new JsonResponse($response);
        } catch (Exception $e) {
            return new JsonResponse(new ErrorResponse([$e->getMessage()]), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /** @Route("/books", name="books_post", methods={"POST"}) */
    public function post(BookRepository $bookRepository) {
        return new JsonResponse(new ErrorResponse(["Endpoint not implemented"]), Response::HTTP_NOT_FOUND);
    }

    /** @Route("/books", name="books_put", methods={"PUT"}) */
    public function put(BookRepository $bookRepository) {
        return new JsonResponse(new ErrorResponse(["Endpoint not implemented"]), Response::HTTP_NOT_FOUND);
    }

    /** @Route("/books", name="books_patch", methods={"PATCH"}) */
    public function patch(BookRepository $bookRepository) {
        return new JsonResponse(new ErrorResponse(["Endpoint not implemented"]), Response::HTTP_NOT_FOUND);
    }

    /** @Route("/books", name="books_delete", methods={"DELETE"}) */
    public function delete(BookRepository $bookRepository) {
        return new JsonResponse(new ErrorResponse(["Endpoint not implemented"]), Response::HTTP_NOT_FOUND);
    }
}