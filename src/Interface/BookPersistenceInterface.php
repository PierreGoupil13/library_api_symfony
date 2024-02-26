<?php

namespace App\Interface;

use App\Entity\Book;

interface BookPersistenceInterface
{
    public function save(Book $book): Book;
    public function delete(Book $book): Book;
    public function findOneById($value): ?Book;

}