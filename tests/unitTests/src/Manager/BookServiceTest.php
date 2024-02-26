<?php

namespace App\Tests\unitTests\src\Manager;

use App\Entity\Book;
use App\Factory\BookFactory;
use App\Interface\BookPersistenceInterface;
use App\Service\BookService;
use JetBrains\PhpStorm\NoReturn;
use PHPUnit\Framework\TestCase;
use Zenstruck\Foundry\Test\Factories;

class BookServiceTest extends TestCase
{
    use Factories;
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
        $fakeBook = BookFactory::new()->withoutPersisting()->create();

        /*
         * Act
         */

        $result = $this->bookService->createBook($fakeBook->object());

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