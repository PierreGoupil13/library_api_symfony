<?php

namespace App\Controller;

use App\Entity\Book;
use App\Interface\AuthorServiceInterface;
use App\Interface\BookServiceInterface;
use App\Repository\BookRepository;
use App\Service\BookService;
use \Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use function PHPUnit\Framework\throwException;

#[Route("book")]
class BookController extends AbstractController
{
    private BookServiceInterface $bookService;
    private AuthorServiceInterface $authorService;
    private SerializerInterface $serializer;
    public function __construct(BookServiceInterface $bookService, AuthorServiceInterface $authorService, SerializerInterface $serializer)
    {
        $this->bookService = $bookService;
        $this->authorService = $authorService;
        $this->serializer = $serializer;
    }

    #[Route("/create")]
    public function createBook(Request $request): Response
    {
        $book = $this->serializer->deserialize($request->getContent(), Book::class, 'json');
        $payload = json_decode($request->getContent(), true);
        if(array_key_exists("authorId",$payload)) {
           $book->setAuthor($this->authorService->getAuthorById($payload["authorId"]));
        }
        $newBook = $this->bookService->createBook($book);
        return new Response($this->json($newBook,201,[], ["groups" => ["book"]]), 201);
    }

    #[Route("/delete/{id}")]
    public function deleteBook(int $id): \Exception|Response
    {
        if ($this->bookService->deleteBookById($id))
        {
            return new Response("Book Deleted", 200);

        }

        return new \Exception('The id does not exist');
    }

}