<?php

namespace App\DataFixtures;

use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Produit;
use App\Entity\Categorie;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $categories = [];
        for ($i = 0; $i < 5; $i++) {
            $categorie = new Categorie();
            $categorie->setNom($faker->word());
            $manager->persist($categorie);
            $categories[] = $categorie;
        }

        for ($i = 0; $i < 20; $i++) {
            $produit = new Produit();
            $produit->setNom($faker->word());
            $produit->setDescription($faker->paragraph(3));
            $produit->setReference($faker->randomNumber( 5, $strict = false));
            $produit->setPrix($faker->randomNumber( 5, $strict = false));
            $produit->setCategorie($faker->randomElement($categories));
            $produit->setImage($faker->imageUrl($width = 640, $height = 480));

            $manager->persist($produit);
        }

        $manager->flush();
    }
}
