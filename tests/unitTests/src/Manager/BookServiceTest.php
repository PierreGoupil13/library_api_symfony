<?php

namespace App\Tests\unitTests\src\Manager;

use App\Controller\BookController;
use App\Entity\Book;
use App\Interface\BookPersistenceInterface;
use App\Service\BookService;
use App\Repository\BookRepository;
use JetBrains\PhpStorm\NoReturn;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class BookServiceTest extends TestCase
{

    #[NoReturn] public function testCreateBookReturnsBook()
    {
        /*
         * Arrange
         */

        // Create a mock for the BookRepository
        $bookRepositoryMock = $this->getMockBuilder(BookPersistenceInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        // Create an instance of the BookController with the mocked BookRepository
        $bookService = new BookService($bookRepositoryMock);
        $fakeBook = new Book();

        /*
         * Act
         */

        // Call the createBook function
        $result = $bookService->createBook($fakeBook);

        /*
         * Assert
         */

        // Assert that the result is what you expect
        $this->assertInstanceOf(Book::class, $result);
    }


}