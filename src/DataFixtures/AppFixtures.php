<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;

class AppFixtures extends Fixture
{

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        for ($i = 0; $i < 20; $i++) {
            $book = new Book();
            $book->setTitle($faker->catchPhrase());
            $book->setCoverText($faker->text());
            $manager->persist($book);
        }

        $manager->flush();
    }
}
