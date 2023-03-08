<?php

namespace App\DataFixtures;

use App\Factory\JobFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $floran = UserFactory::createOne([
            'email' => 'contact@floran.fr',
        ]);
        JobFactory::createOne([
            'title' => 'DevOps',
            'published' => true,
            'owner' => $floran,
        ]);

        $nicolas = UserFactory::createOne([
            'email' => 'contact@nclshart.net',
        ]);
        JobFactory::createOne([
            'title' => 'Développeur fullstack Symfony',
            'published' => true,
            'owner' => $nicolas,
        ]);

        JobFactory::createMany(5);
    }
}
