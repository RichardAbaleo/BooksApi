<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BookController extends AbstractController
{
    // Find all Books
    #[Route('/api/books', name: 'allBooks', methods: ['GET'])]
    public function getAllBooks(BookRepository $bookRepository, SerializerInterface $serializer): JsonResponse
    {
        $bookList = $bookRepository->findAll();
        $jsonBookList = $serializer->serialize($bookList, 'json', ['groups' => 'getBooks']);

        return new JsonResponse(
            $jsonBookList,
            Response::HTTP_OK,
            [],
            true
        );
    }

    // Find Book by ID
    #[Route('/api/books/{id}', name: 'book', methods: ['GET'])]
    public function getBook(Book $book, SerializerInterface $serializer): JsonResponse
    {
        $jsonBook = $serializer->serialize($book, 'json', ['groups' => 'getBooks']);
        return new JsonResponse(
            $jsonBook,
            Response::HTTP_OK,
            [],
            true
        );
    }

    // Delete Book by ID
    #[Route('/api/books/{id}', name: 'deleteBook', methods: ['DELETE'])]
    public function deleteBook(Book $book, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($book);
        $em->flush();
        return new JsonResponse(
            null,
            Response::HTTP_NO_CONTENT
        );
    }

    // Create Book by ID
    #[Route('/api/books', name: "createBook", methods: ['POST'])]
    public function createBook(Request $request, SerializerInterface $serializer, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator, AuthorRepository $authorRepository): JsonResponse
    {
        $book = $serializer->deserialize($request->getContent(), Book::class, 'json');

        $content = $request->toArray();

        // idAuthor takes value OR set value to NULL
        $idAuthor = $content["idAuthor"] ?? -1;

        $book->setAuthor($authorRepository->find($idAuthor));

        $em->persist($book);
        $em->flush();

        $jsonBook = $serializer->serialize($book, 'json', ['groups' => 'getBooks']);
        $location = $urlGenerator->generate('book', ['id' => $book->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

        return new JsonResponse(
            $jsonBook,
            Response::HTTP_CREATED,
            ["Location" => $location],
            true
        );
    }
}
