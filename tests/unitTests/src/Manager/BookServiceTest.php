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

    private $bookRepositoryMock;
    private BookService $bookService;
    public function setUp(): void
    {
        parent::setUp();
        $this->bookRepositoryMock = $this->getMockBuilder(BookPersistenceInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->bookService = new BookService($this->bookRepositoryMock);
    }

    #[NoReturn] public function testCreateBookReturnsBook()
    {
        /*
         * Arrange
         */

        $fakeBook = new Book();

        /*
         * Act
         */

        $result = $this->bookService->createBook($fakeBook);

        /*
         * Assert
         */

        $this->assertInstanceOf(Book::class, $result);
    }
    #[NoReturn] public function testCreateBookIsSameBook()
    {
        /*
         * Arrange
         */

        $fakeBook = new Book();
        $fakeBook ->setTitle("Dune")
            ->setPages(555)
            ->setCategory("Fantasy")
            ->setSynopsis("un livre");

        /*
         * Act
         */

        $result = $this->bookService->createBook($fakeBook);

        /*
         * Assert
         */

        $this->assertEquals($fakeBook->getCategory(),$result->getCategory());
        $this->assertEquals($fakeBook->getTitle(),$result->getTitle());
        $this->assertEquals($fakeBook->getPages(),$result->getPages());
        $this->assertEquals($fakeBook->getSynopsis(),$result->getSynopsis());
    }
    #[NoReturn] public function testCreateBookThrowError()
    {
        /*
         * Arrange
         */

        $fakeBook = "Le Book";

        /*
         * Assert
         */

        $this->expectException(\TypeError::class);
        /*
         * Act
         */

        $this->bookService->createBook($fakeBook);
    }
    #[NoReturn] public function testDeleteBookReturnsTrueWithCorrectId()
    {
        /*
         * Arrange
         */

        $fakeBook = new Book();
        $bookRepositoryMock = $this->createMock(BookPersistenceInterface::class);
        $bookRepositoryMock->expects($this->any())
            ->method('findOneById')
            ->willReturn($fakeBook);
        $bookService = new BookService($bookRepositoryMock);
        /*
         * Act
         */

        $result = $bookService->deleteBookById(1);
        /*
         * Assert
         */

        $this->assertTrue($result);
    }
    #[NoReturn] public function testDeleteBookReturnsFalseWithWrongId()
    {
        /*
         * Arrange
         */

        $fakeBook = null;
        $bookRepositoryMock = $this->createMock(BookPersistenceInterface::class);
        $bookRepositoryMock->expects($this->any())
            ->method('findOneById')
            ->willReturn($fakeBook);
        $bookService = new BookService($bookRepositoryMock);
        /*
         * Act
         */

        $result = $bookService->deleteBookById(1);
        /*
         * Assert
         */

        $this->assertFalse($result);
    }



}