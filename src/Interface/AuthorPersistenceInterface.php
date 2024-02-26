<?php

namespace App\Interface;

use App\Entity\Author;

interface AuthorPersistenceInterface
{
    public function save(Author $author): Author;
    public function delete(Author $author): Author;
    public function findOneById($value): ?Author;
}