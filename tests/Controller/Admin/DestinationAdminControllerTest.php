<?php

namespace App\Tests\Controller\Admin;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\Destination;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DestinationAdminControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $entityManager;
    private mixed $passwordHasher;
    private string $imagesDirectory;


    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get('doctrine')->getManager();
        $this->passwordHasher = $this->client->getContainer()->get(UserPasswordHasherInterface::class);
        $this->imagesDirectory = $this->client->getContainer()->getParameter('images_directory');
        $this->logIn();
        $this->loadFixtures();
    }

    private function logIn(): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'email@example.com']);

        if (!$user) {
            $user = new User();
            $user->setEmail('email@example.com');
            $user->setRoles(['ROLE_ADMIN']);
            $hashedPassword = $this->passwordHasher->hashPassword($user, '123456');
            $user->setPassword($hashedPassword);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }
        $this->client->loginUser($user);
    }

    private function loadFixtures(): void
    {
        $existingDestination = $this->entityManager->getRepository(Destination::class)->findOneBy(['name' => 'Paris']);
        if (empty($existingDestination)) {
            $destination = new Destination();
            $destination->setName('Paris');
            $destination->setDescription('The city of lights');
            $destination->setPrice(120.5);
            $destination->setDuration('3 days');
            $destination->setImage('fixtures/2.jpg');

            $this->entityManager->persist($destination);
            $this->entityManager->flush();
        }
    }

    public function testAdminList(): void
    {
        $this->client->request('GET', $_SERVER['APP_HOST'] . '/admin/destinations/');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Destination List');
    }

    public function testAdminCreate(): void
    {
        $imagePath = $this->imagesDirectory . '/fixtures/1.jpg';
        $crawler = $this->client->request('GET', $_SERVER['APP_HOST'] . '/admin/destinations/new');

        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Save')->form([
            'destination[name]' => 'London',
            'destination[description]' => 'The capital of England',
            'destination[price]' => 150.0,
            'destination[duration]' => '4 days',
            'destination[image]' => new UploadedFile(
                $imagePath,
                '1.jpg',
                'image/jpeg',
                null,
                true
            ),
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/destinations/');
        $this->client->followRedirect();
    }

    public function testAdminEdit(): void
    {
        $destination = $this->entityManager->getRepository(Destination::class)->findOneBy(['name' => 'Paris']);

        $crawler = $this->client->request('GET', $_SERVER['APP_HOST'] . '/admin/destinations/' . $destination->getId() . '/edit');

        $this->assertResponseIsSuccessful();
        $form = $crawler->selectButton('Update')->form([
            'destination[price]' => 200.0,
        ]);

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/destinations/');
        $this->client->followRedirect();

        $updatedDestination = $this->entityManager->getRepository(Destination::class)->find($destination->getId());
        $this->assertEquals(200.0, $updatedDestination->getPrice());
    }



    protected function tearDown(): void
    {
        parent::tearDown();
        $this->entityManager->close();
    }
}
