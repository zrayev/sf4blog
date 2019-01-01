<?php

namespace App\DataFixtures;

use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class AuthorFixtures extends Fixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        for ($i = 0; $i < 5; ++$i) {
            $author = new Author();
            $author->setName($faker->firstName);
            $manager->persist($author);
        }

        $manager->flush();
        $this->addReference('author', $author);
    }

    public function getOrder()
    {
        return 10;
    }
}
