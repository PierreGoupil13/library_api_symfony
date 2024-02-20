<?php

namespace App\Interface;

use App\Entity\Author;

interface AuthorServiceInterface
{
    public function createAuthor(Author $author): Author;
    public function deleteAuthorById(int $id): bool;
    public function getAuthorById(int $id): false|Author;

}