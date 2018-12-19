<?php

namespace App\DataFixtures;

use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class PostFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        for ($i = 0; $i < 5; ++$i) {
            $post = new Post();
            $post->setTitle($faker->sentence($nbWords = 5, $variableNbWords = true));
            $post->setDescription($faker->realText($maxNbChars = 120, $indexSize = 2));
            $post->setBody($faker->realText($maxNbChars = 600, $indexSize = 2));
            $post->setStatus(random_int(0, 2));
            $post->setAuthor($this->getReference('author'));
            $post->setCategory($this->getReference('category'));
            $manager->persist($post);
        }

        $manager->flush();
    }
}