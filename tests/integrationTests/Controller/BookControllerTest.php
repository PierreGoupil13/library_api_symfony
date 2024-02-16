<?php

namespace App\Tests\integrationTests\Controller;

use App\Controller\BookController;
use App\Interface\BookPersistenceInterface;
use App\Tests\integrationTests\IntegrationTestTools;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class BookControllerTest extends IntegrationTestTools
{
    public function testCreateBook()
    {
        /*
         * Arrange
         */

        $bookController = $this->container->get(BookController::class);
        $title = "Dune";
        $fakeRequest = new Request([], [], [], [], [], [], '
            {
            "title" : "Dune",
            "category" : "Fantasy",
            "pages" : 555,
            "synopsis" : "un livre"
            }');

        /*
         * Act
         */

        $result = $bookController->createBook($fakeRequest);
        $bookPersistence = $this->container->get(BookPersistenceInterface::class);

        $boodResult = $bookPersistence->findBy(["title" => $title]);

        /*
         * Assert
         */

        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals($title,$boodResult[0]->getTitle());
    }
}