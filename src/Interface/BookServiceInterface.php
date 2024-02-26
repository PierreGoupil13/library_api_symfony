<?php

namespace App\Interface;

use App\Entity\Author;
use App\Entity\Book;

interface BookServiceInterface
{
    public function createBook(Book $book): Book;
    public function deleteBookById(int $id): bool;
}