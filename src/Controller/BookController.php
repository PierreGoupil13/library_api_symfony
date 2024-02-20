<?php

namespace App\Controller;

use App\Entity\Book;
use App\Interface\BookServiceInterface;
use App\Repository\BookRepository;
use App\Service\BookService;
use \Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route("book")]
class BookController extends AbstractController
{
    private BookServiceInterface $bookService;
    private SerializerInterface $serializer;
    public function __construct(BookServiceInterface $bookService, SerializerInterface $serializer)
    {
        $this->bookService = $bookService;
        $this->serializer = $serializer;
    }

    #[Route("/create")]
    public function createBook(Request $request): Response
    {
        $book = $this->serializer->deserialize($request->getContent(), Book::class, 'json');
        $newBook = $this->bookService->createBook($book);
        return new Response($this->json($newBook), 201);
    }

    #[Route("/delete/{id}")]
    public function deleteBook(int $id): Response
    {
        $newBook = $this->bookService->deleteBookById($id);
        return new Response($this->json($newBook), 200);
    }

}