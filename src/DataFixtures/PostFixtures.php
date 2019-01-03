<?php

namespace App\DataFixtures;

use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class PostFixtures extends Fixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        for ($i = 0; $i < 100; ++$i) {
            $post = new Post();
            $post->setTitle($faker->sentence($nbWords = 5, $variableNbWords = true));
            $post->setDescription($faker->realText($maxNbChars = 120, $indexSize = 2));
            $post->setBody($faker->realText($maxNbChars = 1600, $indexSize = 2));
            $post->setStatus(random_int(0, 2));
            $post->setAuthor($this->getReference('user.admin'));
            $post->setCategory($this->getReference('category'));
            $post->addTag($this->getReference('tag'));
            $post->addComment($this->getReference('comment'));

            $manager->persist($post);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 50;
    }
}
