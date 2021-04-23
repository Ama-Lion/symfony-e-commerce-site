<?php

namespace App\DataFixtures;

use Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for($i = 0; $i < 75; $i++){
            $product = new Product();
            $product->setName($faker->sentence())
                    ->setPrice(mt-rand(100, 200))
                    ->setSlug($faker->slug());
            $manager->persist($product);
        }

        $manager->flush();
    }
}
