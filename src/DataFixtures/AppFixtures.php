<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
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

        for ($i = 0; $i < 10; ++$i) {
            $category = new Category();
            $category->setTitle($faker->asciify('category **'));
            $manager->persist($category);
        }

        for ($i = 0; $i < 20; ++$i) {
            $tag = new Tag();
            $tag->setTitle($faker->word);
            $manager->persist($tag);
        }

        for ($i = 0; $i < 20; ++$i) {
            $comment = new Comment();
            $comment->setTitle($faker->name);
            $comment->setBody($faker->realText($maxNbChars = 200, $indexSize = 2));
            $manager->persist($comment);
        }

        $manager->flush();
    }
}
