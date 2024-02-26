<?php

namespace App\Service;

use App\Entity\Author;
use App\Interface\AuthorPersistenceInterface;
use App\Interface\AuthorServiceInterface;

class AuthorService implements AuthorServiceInterface
{
    private AuthorPersistenceInterface $authorPersistence;
    public function __construct(AuthorPersistenceInterface $authorPersistence)
    {
        $this->authorPersistence = $authorPersistence;
    }
    public function createAuthor(Author $author): Author
    {
        $newAuthor = new Author();
        $newAuthor = $author;
        $this->authorPersistence->save($newAuthor);
        return $newAuthor;
    }

    public function deleteAuthorById(int $id): bool
    {
        $author = $this->authorPersistence->findOneById($id);
        if($author) {
            $this->authorPersistence->delete($author);
            return true;
        }
        return false;
    }

    public function getAuthorById(int $id): false|Author
    {
        $author = $this->authorPersistence->findOneById($id);
        if($author) {
            return $author;
        }
        return false;
    }
}