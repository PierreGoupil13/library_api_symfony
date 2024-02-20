<?php

namespace App\Controller;

use App\Entity\Author;
use App\Interface\AuthorServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route("author")]
class AuthorController extends AbstractController
{

    private AuthorServiceInterface $authorService;
    private SerializerInterface $serializer;
    public function __construct(AuthorServiceInterface $authorService, SerializerInterface $serializer)
    {
        $this->authorService = $authorService;
        $this->serializer = $serializer;
    }

    #[Route("/create")]
    public function createAuthor(Request $request): Response
    {
        $author = $this->serializer->deserialize($request->getContent(), Author::class, 'json');
        $newAuthor = $this->authorService->createAuthor($author);
        return new Response($this->json($newAuthor), 201);
    }

    #[Route("/delete/{id}")]
    public function deleteAuthor(int $id): \Exception|Response
    {
        if ($this->authorService->deleteAuthorById($id))
        {
            return new Response("Author Deleted", 200);

        }

        return new \Exception('The id does not exist');
    }
}