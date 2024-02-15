<?php

namespace App\Service;

use App\Entity\Author;
use App\Entity\Book;
use App\Interface\BookPersistenceInterface;
use App\Interface\BookServiceInterface;

class BookService implements BookServiceInterface
{
    private BookPersistenceInterface $bookPersistence;
    public function __construct(BookPersistenceInterface $bookPersistence)
    {
        $this->bookPersistence = $bookPersistence;
    }
    public function createBook(Book $book): Book
    {
        $newBook = $this->bookPersistence->save($book);
        return $newBook;
    }
}