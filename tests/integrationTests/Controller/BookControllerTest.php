<?php

namespace App\Tests\integrationTests\Controller;

use App\Controller\BookController;
use App\Entity\Book;
use App\Service\BookService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

class BookControllerTest extends KernelTestCase
{

    public function setUp(): void
    {
        self::bootKernel();
    }

    public function testCreateBook()
    {
        /*
         * Arrange
         */

        $container = static::getContainer();

        $bookController = $container->get(BookController::class);

        $fakeRequest = new Request([], [], [], [], [], [], '
            {
            "title" : "random book",
            "category" : "Fantasy",
            "pages" : 555,
            "synopsis" : "un livre"
            }');

        /*
         * Act
         */

        $result = $bookController->createBook($fakeRequest);

        /*
         * Assert
         */

        $this->assertInstanceOf(Book::class, $result);
    }
}