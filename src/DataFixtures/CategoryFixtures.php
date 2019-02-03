<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CategoryFixtures extends Fixture implements OrderedFixtureInterface
{
    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $masterCategory1 = new Category();
        $masterCategory1->setTitle('Live');
        $subCategory1_1 = new Category();
        $subCategory1_1->setTitle('Sport');
        $subCategory1_1->setParent($masterCategory1);
        $subCategory1_2 = new Category();
        $subCategory1_2->setTitle('Peoples');
        $subCategory1_2->setParent($masterCategory1);
        $manager->persist($masterCategory1);
        $manager->persist($subCategory1_1);
        $manager->persist($subCategory1_2);

        $manager->flush();

        $masterCategory2 = new Category();
        $masterCategory2->setTitle('Category2');
        $subCategory2_1 = new Category();
        $subCategory2_1->setTitle('Category 2 - 1');
        $subCategory2_1->setParent($masterCategory2);
        $subCategory2_2 = new Category();
        $subCategory2_2->setTitle('Category 2 -2');
        $subCategory2_2->setParent($masterCategory2);
        $manager->persist($masterCategory2);
        $manager->persist($subCategory2_1);
        $manager->persist($subCategory2_2);
        $manager->flush();

        $this->addReference('category', $masterCategory2);
    }

    public function getOrder()
    {
        return 20;
    }
}
