<?php

namespace App\Tests\integrationTests\Controller;

use App\Controller\BookController;
use App\Entity\Book;
use App\Interface\BookPersistenceInterface;
use App\Tests\integrationTests\IntegrationTestTools;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;

class BookControllerTest extends IntegrationTestTools
{
    private BookController $bookController;
    private BookPersistenceInterface $bookPersistence;

    public function setUp(): void
    {
        parent::setUp();
        $this->bookController = $this->container->get(BookController::class);
        $this->bookPersistence = $this->container->get(BookPersistenceInterface::class);
    }

    private function fakeTitleRequest(string $title): Request
    {
        return new Request([], [], [], [], [], [], json_encode(
            [
                "title" => $title,
                "category" => "Fantasy",
                "pages" => 555,
                "synopsis" => "un livre"
            ]));
    }

    private function fakeTitleBook(string $title): Book
    {
        $book = new Book();
        $book ->setTitle($title)
            ->setPages(555)
            ->setCategory("Fantasy")
            ->setSynopsis("un livre");

        return $book;
    }
    public function testCreateBookIsCorrectAndSuccessful()
    {
        /*
         * Arrange
         */
        $title = "DuneTestBook";
        $fakeRequest = $this->fakeTitleRequest($title);
        $expectedBook = $this->fakeTitleBook($title);
        /*
         * Act
         */

        $result = $this->bookController->createBook($fakeRequest);

        // Assure que le resultat d'un futur findBy ne soit pas du cache
        self::$kernel->getContainer()->get('doctrine')->getManager()->clear();

        $bookResult = $this->bookPersistence->findBy(["title" => $title]);
        /*
         * Assert
         */

        // Is Successfull
        $this->assertEquals(201, $result->getStatusCode());

        // Expected Book Details
        $this->assertNotEmpty($bookResult, "The book with title '$title' should be persisted.");
        $this->assertEquals($expectedBook->getCategory(),$bookResult[0]->getCategory());
        $this->assertEquals($expectedBook->getTitle(),$bookResult[0]->getTitle());
        $this->assertEquals($expectedBook->getPages(),$bookResult[0]->getPages());
        $this->assertEquals($expectedBook->getSynopsis(),$bookResult[0]->getSynopsis());
    }

    public function testCreateBookIsPersisted()
    {
        /*
         * Arrange
         */
        $title = "DuneTestBook";
        $fakeRequest = $this->fakeTitleRequest($title);
        /*
         * Act
         */

        $result = $this->bookController->createBook($fakeRequest);

        // Assure que le resultat d'un futur findBy ne soit pas du cache
        self::$kernel->getContainer()->get('doctrine')->getManager()->clear();

        $bookResult = $this->bookPersistence->findBy(["title" => $title]);
        /*
         * Assert
         */

        // Is Successfull
        $this->assertEquals(201, $result->getStatusCode());

        // Expected Book Details
        $this->assertNotEmpty($bookResult, "The book with title '$title' should be persisted.");
        $this->assertInstanceOf(Book::class, $bookResult[0]);
    }

    public function testCreateBookErrorWhenWrongType()
    {
        /*
         * Arrange
         */
        $title = "DuneTestBook";
        $fakeRequestIncomplete = new Request([], [], [], [], [], [], json_encode(
            [
                "title" => $title,
                "category" => 11,
            ]));

        /*
         * Act And Assert
         */
        $this->expectException(NotNormalizableValueException::class);

        $this->bookController->createBook($fakeRequestIncomplete);

    }

    public function testCreateBookErrorWhenIncomplete()
    {
        /*
         * Arrange
         */
        $title = "DuneTestBook";
        $fakeRequestIncomplete = new Request([], [], [], [], [], [], json_encode(
            [
                "title" => $title,
                "category" => "11",
            ]));

        /*
         * Act And Assert
         */
        $this->expectException(NotNullConstraintViolationException::class);

        $this->bookController->createBook($fakeRequestIncomplete);

    }
}