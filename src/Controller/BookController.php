<?php

namespace App\Controller;

use App\Entity\Book;
use App\Interface\BookServiceInterface;
use App\Repository\BookRepository;
use App\Service\BookService;
use \Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\SerializerInterface;

class BookController extends AbstractController
{
    private BookServiceInterface $bookService;
    private SerializerInterface $serializer;
    public function __construct(BookServiceInterface $bookService, SerializerInterface $serializer)
    {
        $this->bookService = $bookService;
        $this->serializer = $serializer;
    }

    public function createBook(Request $request): Book
    {
        $book = $this->serializer->deserialize($request->getContent(), Book::class, 'json');
        return $this->bookService->createBook($book);
    }

}