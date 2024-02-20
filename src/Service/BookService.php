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
        $newBook = new Book();
        $newBook = $book;
        $this->bookPersistence->save($newBook);
        return $newBook;
    }

    public function deleteBookById(int $id): bool
    {
        $book = $this->bookPersistence->findOneById($id);
        if($book) {
            $this->bookPersistence->delete($book);
            return true;
        }
        return false;
    }
}