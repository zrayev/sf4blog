<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class CategoryFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        for ($i = 0; $i < 10; ++$i) {
            $category = new Category();
            $category->setTitle($faker->asciify('category **'));
            $manager->persist($category);
        }

        $manager->flush();
        $this->addReference('category', $category);
    }
}
