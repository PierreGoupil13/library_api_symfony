<?php

namespace App\Service;

use App\Entity\Author;
use App\Entity\Book;
use App\Interface\BookServiceInterface;

class BookService implements BookServiceInterface
{

    public function createBook(Book $book): Book
    {
        return $book;
    }
}