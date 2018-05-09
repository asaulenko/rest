<?php

namespace AppBundle\DataFixtures;

use AppBundle\Entity\Person;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadPersonData extends Fixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $person = new Person();
        $person->setDateOfBirth(new \DateTime('1956-07-09'));
        $person->setFirstName('Tom');
        $person->setLastName('Hanks');

        $manager->persist($person);
        $manager->flush();
    }
}
