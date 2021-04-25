<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    protected $slugger;
    protected $encoder;
    public function __construct(SluggerInterface $slugger, UserPasswordEncoderInterface $encoder)
    {
        $this->slugger = $slugger;
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new \Liior\Faker\Prices($faker));
        $faker->addProvider(new \Bezhanov\Faker\Provider\Commerce($faker));
        $faker->addProvider(new \Bluemmb\Faker\PicsumPhotosProvider($faker));

        $admin = new User();
        $hashedPassword = $this->encoder->encodePassword($admin, "password");

        $admin->setEmail("admin@gmail.com")
        ->setPassword($hashedPassword)
        ->setFirstName("admin")
        ->setLastName("lion")
        ->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);
        

        for($u = 0; $u < 4; $u++){
            $user = new User();
            $hashedPassword = $this->encoder->encodePassword($user, "password");
            $user->setEmail("user$u@gmail.com")
                ->setFirstName($faker->firstName())
                ->setLastName($faker->lastName())
                ->setPassword($hashedPassword);
            
            $manager->persist($user);
        }

        for($c = 0; $c < 3; $c++) {
            $category = new Category;
            $category->setName($faker->department)
                    ->setSlug(strtolower($this->slugger->slug($category->getName())));
            $manager->persist($category);
            for($i = 0; $i < mt_rand(12, 15); $i++){
                $product = new Product();
                $product->setName($faker->productName)
                        ->setPrice($faker->price(4000, 20000))
                        ->setSlug(strtolower($this->slugger->slug($product->getName())))
                        ->setCategory($category)
                        ->setDescription($faker->paragraph)
                        ->setPicture($faker->imageUrl(400, 400, true));
                $manager->persist($product);
            }
        }

        $manager->flush();
    }
}
