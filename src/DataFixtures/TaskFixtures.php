<?php

namespace App\DataFixtures;


use App\Entity\Task;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TaskFixtures extends BaseFixture implements DependentFixtureInterface
{
    /**
     * Load data fixtures with the passed EntityManager
     */
    public function load(ObjectManager $objectManager)
    {
        parent::load($objectManager);
        $this->createMany('Task',5,function(Task $task, $count){
            $task->setUser($this->getReference('user'));
            $task->setTitle($this->faker->title);
            $task->setDescription($this->faker->text);
            $dateTime = $this->faker->dateTime('+2 years', 'Europe/Berlin');
            $task->setStartTime($dateTime->format('Y-m-d H:i:s'));
            $task->setDuration($this->faker->numberBetween(10,100));
            $task->setStatus($this->faker->randomElement(['TODO', 'DOING', 'DONE']));
        });
        $objectManager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }
}