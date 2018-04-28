<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadMovieData extends Fixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $movie = new Movie();
        $movie->setTitle('Green Mile');
        $movie->setYear(1999);
        $movie->setTime(130);
        $movie->setDescription('Great movie');

        $manager->persist($movie);
        $manager->flush();
    }
}
