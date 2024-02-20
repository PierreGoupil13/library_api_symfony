<?php

namespace App\Tests\integrationTests\Controller;

use App\Controller\BookController;
use App\Entity\Book;
use App\Factory\AuthorFactory;
use App\Factory\BookFactory;
use App\Interface\BookPersistenceInterface;
use App\Tests\integrationTests\IntegrationTestTools;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use http\Exception\RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class BookControllerTest extends IntegrationTestTools
{
    use ResetDatabase, Factories;

    private BookController $bookController;
    private BookPersistenceInterface $bookPersistence;

    public function setUp(): void
    {
        parent::setUp();
        $this->bookController = $this->container->get(BookController::class);
        $this->bookPersistence = $this->container->get(BookPersistenceInterface::class);
    }
    private function fakeTitleRequest(Book $book): Request
    {
        return new Request([], [], [], [], [], [], json_encode(
            [
                'title' => $book->getTitle(),
                'category' => $book->getCategory(),
                'pages' => $book->getPages(),
                'synopsis' => $book->getSynopsis(),
            ]
        ));
    }
    public function testCreateBookIsCorrectAndSuccessful()
    {
        /*
         * Arrange
         */
        $title = "Dune";
        // On bloque la persistance afin de pouvoir bien tester la methode
        $book = BookFactory::new()->withoutPersisting()->create(['title' => $title]);
        $fakeRequest = $this->fakeTitleRequest($book->object());
        /*
         * Act
         */

        $result = $this->bookController->createBook($fakeRequest);

        // Assure que le resultat d'un futur findBy ne soit pas du cache
        self::$kernel->getContainer()->get('doctrine')->getManager()->clear();

        $bookResult = $this->bookPersistence->findBy(["category" => $book->getCategory()]);
        /*
         * Assert
         */

        // Is Successfull
        $this->assertEquals(201, $result->getStatusCode());

        // Expected Book Details
        $this->assertNotEmpty($bookResult, "The book with title '$title' should be persisted.");
        $this->assertEquals($book->getCategory(),$bookResult[0]->getCategory());
        $this->assertEquals($book->getTitle(),$bookResult[0]->getTitle());
        $this->assertEquals($book->getPages(),$bookResult[0]->getPages());
        $this->assertEquals($book->getSynopsis(),$bookResult[0]->getSynopsis());
    }

    public function testCreateBookWithAuthorIsCorrectAndSuccessful()
    {
        /*
         * Arrange
         */
        $title = "Dune";
        // On bloque la persistance afin de pouvoir bien tester la methode
        $book = BookFactory::new()->withoutPersisting()->create(['title' => $title]);
        $author = AuthorFactory::createOne();
        $fakeRequest = new Request([], [], [], [], [], [], json_encode(
            [
                'title' => $book->getTitle(),
                'category' => $book->getCategory(),
                'pages' => $book->getPages(),
                'synopsis' => $book->getSynopsis(),
                'authorId' => $author->getId()
            ]
        ));
        /*
         * Act
         */

        $result = $this->bookController->createBook($fakeRequest);

        // Assure que le resultat d'un futur findBy ne soit pas du cache
        self::$kernel->getContainer()->get('doctrine')->getManager()->clear();

        $bookResult = $this->bookPersistence->findBy(["category" => $book->getCategory()]);
        /*
         * Assert
         */

        // Is Successfull
        $this->assertEquals(201, $result->getStatusCode());

        // Expected Book Details
        $this->assertNotEmpty($bookResult, "The book with title '$title' should be persisted.");
        $this->assertEquals($author->object(),$bookResult[0]->getAuthor());
    }
    public function testCreateBookIsPersisted()
    {
        /*
         * Arrange
         */
        $title = "Dune";
        // On bloque la persistance afin de pouvoir bien tester la methode
        $book = BookFactory::new()->withoutPersisting()->create(['title' => $title]);
        $fakeRequest = $this->fakeTitleRequest($book->object());
        /*
         * Act
         */
        $result = $this->bookController->createBook($fakeRequest);
        // Assure que le resultat d'un futur findBy ne soit pas du cache
        self::$kernel->getContainer()->get('doctrine')->getManager()->clear();

        $bookResult = $this->bookPersistence->findBy(["category" => $book->getCategory()]);

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

    public function testDeleteBookIsSuccessfulAndDeleted()
    {
        /*
         * Arrange
         */
        $title = "Dune";
        $book = BookFactory::createOne(['title' => $title]);

        /*
         * Act
         */
        $result = $this->bookController->deleteBook($book->getId());
        // Assure que le resultat d'un futur findBy ne soit pas du cache
        self::$kernel->getContainer()->get('doctrine')->getManager()->clear();

        /*
         * Assert
         */
        $this->assertEquals(200, $result->getStatusCode());
        $this->expectExceptionMessage('The object no longer exists.');
        $bookResult = $this->bookPersistence->findBy(["id" => $book->getId()]);


    }

    public function testDeleteBookErrorWhenWrongId()
    {
        /*
         * Arrange
         */
        $title = "Dune";
        $book = BookFactory::createOne(['title' => $title]);

        /*
         * Act
         */
        $result = $this->bookController->deleteBook($book->getId() + 10);
        // Assure que le resultat d'un futur findBy ne soit pas du cache
        self::$kernel->getContainer()->get('doctrine')->getManager()->clear();

        /*
         * Assert
         */
        $this->assertEquals(0, $result->getCode());
        $this->assertEquals('The id does not exist', $result->getMessage());
        $bookResult = $this->bookPersistence->findBy(["id" => $book->getId()]);
        $this->assertEquals($book->object(),$bookResult[0]);


    }

}