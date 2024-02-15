<?php

namespace App\Interface;

use App\Entity\Book;

interface BookPersistenceInterface
{
    public function save(Book $book): Book;
}