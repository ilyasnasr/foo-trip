<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Destination;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory as FakerFactory;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setRoles(['ROLE_ADMIN']);

        $hashedPassword = $this->passwordHasher->hashPassword($admin, '123456');
        $admin->setPassword($hashedPassword);

        $manager->persist($admin);

        $faker = FakerFactory::create();
        for ($i = 0; $i < 10; $i++) {
            $destination = new Destination();
            $destination->setName($faker->city);
            $destination->setDescription($faker->paragraph);
            $destination->setPrice($faker->randomFloat(2, 50, 1000));
            $destination->setDuration($faker->numberBetween(1, 14) . ' days');
            $destination->setImage('fixtures/' . $faker->numberBetween(1, 5) . '.jpg');

            $manager->persist($destination);
        }
        $manager->flush();
    }
}
