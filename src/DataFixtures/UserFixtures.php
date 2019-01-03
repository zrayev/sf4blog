<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $testPassword = 'test';

        $user = new User();
        $user->setUsername('admin');
        $user->setFirstName('Site');
        $user->setLastName('Admin');
        $user->setEmail('admin@example.com');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setEnabled(true);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            $testPassword
        ));

        $manager->persist($user);
        $manager->flush();
        $this->addReference('user.admin', $user);
        unset($user);

        $user = new User();
        $user->setUsername('superadmin');
        $user->setFirstName('Super');
        $user->setLastName('Admin');
        $user->setEmail('superadmin@example.com');
        $user->setRoles(['ROLE_SUPER_ADMIN']);
        $user->setEnabled(true);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            $testPassword
        ));

        $manager->persist($user);
        $manager->flush();
        $this->addReference('user.superadmin', $user);
        unset($user);

        $user = new User();
        $user->setUsername('user');
        $user->setFirstName('John');
        $user->setLastName('Doe');
        $user->setEmail('user@example.com');
        $user->setRoles(['ROLE_USER']);
        $user->setEnabled(true);
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            $testPassword
        ));

        $manager->persist($user);
        $manager->flush();
        $this->addReference('user', $user);
        unset($user);

        $faker = Factory::create();
        for ($i = 0; $i < 5; ++$i) {
            $user = new User();
            $user->setUsername($faker->userName);
            $user->setFirstName($faker->firstName);
            $user->setLastName($faker->lastName);
            $user->setEmail($faker->safeEmail);
            $user->setRoles(['ROLE_USER']);
            $user->setEnabled(true);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $testPassword
            ));

            $manager->persist($user);
        }
        $manager->flush();
    }
}
