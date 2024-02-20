<?php

namespace App\Tests\integrationTests\Controller;

use App\Controller\AuthorController;
use App\Entity\Author;
use App\Entity\Book;
use App\Factory\AuthorFactory;
use App\Factory\BookFactory;
use App\Interface\AuthorPersistenceInterface;
use App\Tests\integrationTests\IntegrationTestTools;
use Doctrine\DBAL\Exception\NotNullConstraintViolationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class AuthorControllerTest extends IntegrationTestTools
{
    use ResetDatabase, Factories;

    private AuthorController $authorController;
    private AuthorPersistenceInterface $authorPersistence;

    public function setUp(): void
    {
        parent::setUp();
        $this->authorController = $this->container->get(AuthorController::class);
        $this->authorPersistence = $this->container->get(AuthorPersistenceInterface::class);
    }
    private function fakeAuthorRequest(Author $author): Request
    {
        return new Request([], [], [], [], [], [], json_encode(
            [
                'name' => $author->getName(),
                'lastName' => $author->getLastName(),
            ]
        ));
    }
    public function testCreateAuthorIsCorrectAndSuccessful()
    {
        /*
         * Arrange
         */
        // On bloque la persistance afin de pouvoir bien tester la methode
        $author = AuthorFactory::new()->withoutPersisting()->create();
        $fakeRequest = $this->fakeAuthorRequest($author->object());
        /*
         * Act
         */

        $result = $this->authorController->createAuthor($fakeRequest);

        // Assure que le resultat d'un futur findBy ne soit pas du cache
        self::$kernel->getContainer()->get('doctrine')->getManager()->clear();

        $authorResult = $this->authorPersistence->findBy(["name" => $author->getName()]);
        /*
         * Assert
         */

        // Is Successfull
        $this->assertEquals(201, $result->getStatusCode());

        // Expected Book Details
        $this->assertNotEmpty($authorResult, "The book with name should be persisted.");
        $this->assertEquals($author->getName(),$authorResult[0]->getName());
        $this->assertEquals($author->getLastName(),$authorResult[0]->getLastName());
    }

    public function testCreateAuthorIsPersisted()
    {
        /*
         * Arrange
         */
        // On bloque la persistance afin de pouvoir bien tester la methode
        $book = AuthorFactory::new()->withoutPersisting()->create();
        $fakeRequest = $this->fakeAuthorRequest($book->object());
        /*
         * Act
         */
        $result = $this->authorController->createAuthor($fakeRequest);
        // Assure que le resultat d'un futur findBy ne soit pas du cache
        self::$kernel->getContainer()->get('doctrine')->getManager()->clear();

        $authorResult = $this->authorPersistence->findBy(["name" => $book->getName()]);

        /*
         * Assert
         */

        // Is Successfull
        $this->assertEquals(201, $result->getStatusCode());

        // Expected Book Details
        $this->assertNotEmpty($authorResult, "The book with title should be persisted.");
        $this->assertInstanceOf(Author::class, $authorResult[0]);
    }

    public function testCreateAuthorErrorWhenWrongType()
    {
        /*
         * Arrange
         */
        $fakeRequestIncomplete = new Request([], [], [], [], [], [], json_encode(
            [
                "name" => 'Pedro',
                "lastName" => 11,
            ]));

        /*
         * Act And Assert
         */
        $this->expectException(NotNormalizableValueException::class);

        $this->authorController->createAuthor($fakeRequestIncomplete);

    }

    public function testCreateAuthorErrorWhenIncomplete()
    {
        /*
         * Arrange
         */
        $fakeRequestIncomplete = new Request([], [], [], [], [], [], json_encode(
            [
                "name" => 'Pedro',
            ]));

        /*
         * Act And Assert
         */
        $this->expectException(NotNullConstraintViolationException::class);

        $this->authorController->createAuthor($fakeRequestIncomplete);

    }

    public function testDeleteBookIsSuccessfulAndDeleted()
    {
        /*
         * Arrange
         */
        $author = AuthorFactory::createOne();

        /*
         * Act
         */
        $result = $this->authorController->deleteAuthor($author->getId());
        // Assure que le resultat d'un futur findBy ne soit pas du cache
        self::$kernel->getContainer()->get('doctrine')->getManager()->clear();

        /*
         * Assert
         */
        $this->assertEquals(200, $result->getStatusCode());
        $this->expectExceptionMessage('The object no longer exists.');
        $bookResult = $this->authorPersistence->findBy(["id" => $author->getId()]);


    }

    public function testDeleteBookErrorWhenWrongId()
    {
        /*
         * Arrange
         */
        $author = AuthorFactory::createOne();

        /*
         * Act
         */
        $result = $this->authorController->deleteAuthor($author->getId() + 10);
        // Assure que le resultat d'un futur findBy ne soit pas du cache
        self::$kernel->getContainer()->get('doctrine')->getManager()->clear();

        /*
         * Assert
         */
        $this->assertEquals(0, $result->getCode());
        $this->assertEquals('The id does not exist', $result->getMessage());
        $authorResult = $this->authorPersistence->findBy(["id" => $author->getId()]);
        $this->assertEquals($author->object(),$authorResult[0]);


    }

}