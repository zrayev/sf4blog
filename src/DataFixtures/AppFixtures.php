<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Post;
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

        $authors = $manager->getRepository(Author::class)->findAll();
        $categories = $manager->getRepository(Category::class)->findAll();
        $tags = $manager->getRepository(Tag::class)->findAll();
        $comments = $manager->getRepository(Comment::class)->findAll();

        for ($i = 0; $i < 5; ++$i) {
            $post = new Post();
            $post->setTitle($faker->sentence($nbWords = 5, $variableNbWords = true));
            $post->setDescription($faker->realText($maxNbChars = 120, $indexSize = 2));
            $post->setBody($faker->realText($maxNbChars = 600, $indexSize = 2));
            $post->setStatus(random_int(0, 2));
            foreach ($authors as $author) {
                $post->setAuthor($author);
            }
            foreach ($categories as $category) {
                $post->setCategory($category);
            }
            foreach ($tags as $tag) {
                $post->addTag($tag);
            }
            foreach ($comments as $comment) {
                $post->addComment($comment);
            }
            $manager->persist($post);
        }

        $manager->flush();
    }
}
