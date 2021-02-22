<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
        $arrayEntityUser = [];
        $arrayEntityCategory = [];
        $arrayCategoryName=["info","cuisine","brico","deco"];

        foreach ($arrayCategoryName as $categoryName){
            $category = new Category();
            $category
                ->setName($categoryName)
                ;
            array_push($arrayEntityCategory, $category);
            $manager->persist($category);
        }

        $user = new User();
        $user
            ->setEmail('user@dan.fr')
            ->setPassword($this->encoder->encodePassword($user,"user"))
            ->setPseudo($faker->firstName)
            ->setLastname($faker->lastName)
            ->setFirstname($faker->firstName)
            ;

        $manager->persist($user);

        $admin = new  User();
        $admin
            ->setEmail('admin@dan.fr')
            ->setPassword($this->encoder->encodePassword($admin,"admin"))
            ->setPseudo($faker->firstName)
            ->setLastname($faker->lastName)
            ->setFirstname($faker->firstName)
            ->setRoles(['ROLE_ADMIN'])
        ;

        $manager->persist($admin);

        for($i =0; $i <= 20;$i++) {
            $userGen = new User();
            $userGen
                ->setEmail($faker->email)
                ->setPassword($this->encoder->encodePassword($userGen,"user"))
                ->setPseudo($faker->firstName)
                ->setLastname($faker->lastName)
                ->setFirstname($faker->firstName)
                ->setRoles(['ROLE_USER'])
            ;
            array_push($arrayEntityUser,$userGen);
            $manager->persist($userGen);
        }
        for($i =0; $i <= 20;$i++) {
            $articleGen = new Article();
            $articleGen
                ->setContent($faker->text)
                ->setTitle($faker->title)
                ->setCreatedAt($faker->dateTime)
                ->setPicture($faker->url)
                ->setUpdatedAt($faker->dateTime)
                ->setSummarize($faker->text(50))
                ->setAuthor($arrayEntityUser[random_int(0,sizeof($arrayEntityUser)-1)])
                ->setCategory($arrayEntityCategory[random_int(0,sizeof($arrayEntityCategory)-1)])
            ;
            $manager->persist($articleGen);
        }


            $manager->flush();
    }
}
