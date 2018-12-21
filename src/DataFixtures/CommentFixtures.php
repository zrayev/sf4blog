<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class CommentFixtures extends Fixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        for ($i = 0; $i < 20; ++$i) {
            $comment = new Comment();
            $comment->setTitle($faker->name);
            $comment->setBody($faker->realText($maxNbChars = 200, $indexSize = 2));
            $manager->persist($comment);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 30;
    }
}
