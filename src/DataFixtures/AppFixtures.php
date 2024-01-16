<?php

namespace App\DataFixtures;

use App\Entity\Wish;
use Faker\Factory;
use Faker\Generator;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        // Création d'une boucle qui va créer 50 articles aléatoires
        // Chaque article aura un titre, un contenu, une date de publication qui seront générés aléatoirement
        // Utiliser la commande : symfony console doctrine:fixtures:load
        for ($i=1; $i <= 50; $i++) {
            $wish = new Wish();
            $wish->setTitle($this->faker->sentence(4))
                ->setDescription($this->faker->paragraph)
                ->setAuthor('Author' . $i)
                ->setIsPublished(true);
            $manager->persist($wish);
        }
        $manager->flush();
    }
}
