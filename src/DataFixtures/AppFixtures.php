<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use function Symfony\Component\String\u;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        // $product = new Product();
        // $manager->persist($product);
        $user = new User();
        $user
            ->setEmail('user@dan.fr')
            ->setPassword($this->encoder->encodePassword($user,"user"))
            ->setSpeudo($faker->firstName)
            ->setLastname($faker->lastName)
            ->setFirstname($faker->firstName)
            ;

        $manager->persist($user);

        $admin = new  User();
        $admin
            ->setEmail('admin@dan.fr')
            ->setPassword($this->encoder->encodePassword($admin,"admin"))
            ->setSpeudo($faker->firstName)
            ->setLastname($faker->lastName)
            ->setFirstname($faker->firstName)
            ->setRoles(['ROLE_ADMIN'])
        ;

        $manager->persist($admin);

        for($i =0; $i <= 20;$i++) {
            $usergen = new User();
            $usergen
                ->setEmail($faker->email)
                ->setPassword($this->encoder->encodePassword($usergen,"user"))
                ->setSpeudo($faker->firstName)
                ->setLastname($faker->lastName)
                ->setFirstname($faker->firstName)
                ->setRoles(['ROLE_USER'])
            ;
            $manager->persist($usergen);
        }
            $manager->flush();
    }
}
