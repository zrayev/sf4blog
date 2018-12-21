<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class TagFixtures extends Fixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        for ($i = 0; $i < 20; ++$i) {
            $tag = new Tag();
            $tag->setTitle($faker->word);
            $manager->persist($tag);
        }

        $manager->flush();
        $this->addReference('tag', $tag);
    }

    public function getOrder()
    {
        return 40;
    }
}
