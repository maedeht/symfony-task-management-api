<?php

namespace Tests\Unit\Repositories;

use App\DataFixtures\TaskFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\Task;
use App\Entity\User;
use App\Traits\CommonTestTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskRepositoryTest extends KernelTestCase
{
    use CommonTestTrait;

    private $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $kernel = self::bootKernel();

        $this->loadFixtures([
            UserFixtures::class,
            TaskFixtures::class
        ]);

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }

    public function test_creating_task_succeeds_with_required_parameters()
    {
        $user = $this->create_an_unpersist_user();
        $input = [
            'title' => 'task title',
            'start_time' => (new \DateTime('now'))->format('Y-m-d H:i:s')
        ];

        $task = $this->entityManager
            ->getRepository(Task::class)
            ->create($input, $user);

        $this->assertNotNull($task);
    }

    public function test_updating_task_succeeds_with_parameters()
    {
        $user = $this->get_last_user();
        $task = $this->create_task_for_user($user);
        $input = [
            'title' => 'task title',
            'status' => 'DONE'
        ];
        $task = $this->entityManager
            ->getRepository(Task::class)
            ->update($input, $task->getId());

        $this->assertNotNull($task);
    }

    public function test_deleting_task_suceeds()
    {
        $user = $this->get_last_user();
        $task = $this->create_task_for_user($user);
        $task = $this->entityManager
            ->getRepository(Task::class)
            ->delete($task->getId());

        $this->assertNotNull($task);
    }

    public function test_retrieving_all_of_user_tasks()
    {
        $user = $this->get_last_user();
        $task = $this->entityManager
            ->getRepository(Task::class)
            ->userTasks($user->getId());

        $this->assertNotNull($task);
    }
}