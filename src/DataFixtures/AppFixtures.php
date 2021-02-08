<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder){
        $this->encoder=$encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $faker->seed(0);

        $user = new User();
        $user
            ->setEmail('user@ex.com')
            ->setPassword($this->encoder->encodePassword($user,'user'))
            ->setFirstname($faker->firstName)
            ->setLastname($faker->lastName)
            ->setSpeudo("user")
            ;
        $manager->persist($user);

        $admin = new User();
        $admin
            ->setEmail('admin@ex.com')
            ->setPassword($this->encoder->encodePassword($admin,'admin'))
            ->setFirstname($faker->firstName)
            ->setLastname($faker->lastName)
            ->setSpeudo("admin")
        ;

        $manager->persist($admin);
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
