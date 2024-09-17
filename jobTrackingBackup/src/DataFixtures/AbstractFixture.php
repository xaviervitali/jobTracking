<?php

namespace App\DataFixtures;

use Faker\Factory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

abstract class AbstractFixture extends Fixture
{
    /**
     * instance de $faker qui sera dispo dans toutes nos fixtures
     */
    protected $faker;
    /**
     * instance de l'Object Manager  qui sera dispo dans toutes nos fixtures
     */
    protected  $manager;

    /**
     * Function Abstraite qui sera appelée après la function load 
     *
     */
    abstract protected function loadData(ObjectManager $manager);


    /**
     * Initialisation de la Fixture
     *
     */
    final public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->faker =\Faker\Factory::create('fr_FR');
        $this->customizeFaker();

        $this->loadData($manager);
    }
    /**
     * fonction qui renvoi une référence à une entité créee dansune autre fixture auparavant en precisant la class de l'entité que vous rechercher
     */
    public function getRandomreference(string $classname)
    {
        $references = $this->referenceRepository->getReferencesByClass();

        if (empty($references)) {
            throw new \Exception('No references found for class ' . $classname);
        }

        /**
         * on filtre sur les clés de toutes les références crée dans la méthode addToMany
         */
        $filteredNames = array_filter(
            array_keys($references[$classname]),
            function ($names) use ($classname) {
                return strpos($names, $classname) === 0;
            }
        );


        if (count($filteredNames) === 0) {
            throw new \Exception("pas de $classname");
        }
        $randomReferenceName = $this->faker->randomElement($filteredNames);
        return $this->getReference($randomReferenceName);
    }
    /**
     * fonction qui permet d'automatiser la creation de fixture en lui injectant
     *le nom de la classe, le nombre d'occurence et un callable qui represente les parametres de remplisage de la table
     * @param string $classname
     * @param integer $count
     * @param callable $callback
     * @return void
     */
    public function createMany(string $classname, int $count, callable $callback)
    {
        for ($i = 0; $i < $count; $i++) {
            $obj = new  $classname;
            $callback($obj, $i);
            $this->manager->persist($obj);
            $this->addReference($classname . '_' . $i, $obj);
        }
        $this->manager->flush();
    }

    protected function customizeFaker(): void
    {
    }
}