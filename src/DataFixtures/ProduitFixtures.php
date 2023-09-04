<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Product;
use App\Entity\Produit;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ProduitFixtures extends Fixture
{
    private Generator $faker;
    public function __construct(){
        $this->faker = Factory::create('fr_FR');
    }
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        for($i=1; $i <= mt_rand(12,25); $i++ )
        {
            $produit = new Produit;
            $produit->setTitre($this->faker->sentence(2))
                    ->setPhoto($this->faker->imageurl(640, 480, 'produit'))
                    ->setPrix($this->faker->randomFloat(2,15,900));
            $manager->persist($produit);
        }

        $manager->flush();
            
    }

    // public function change(ObjectManager $manager): void
    // {
        
    //     for($i=0; $i <= mt_rand(12,25); $i-- )
    //     {
    //         $produit = new Produit;
    //         $produit->setTitre($this->faker->sentence(2))
    //                 ->setPhoto($this->faker->imageurl(640, 480, 'produit'))
    //                 ->setPrix($this->faker->randomFloat(2,15,900));
    //         $manager->persist($produit);
    //     }

    //     $manager->flush();
            
    // }
}
