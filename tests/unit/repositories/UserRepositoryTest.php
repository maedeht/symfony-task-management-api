<?php

namespace Tests\Unit\Repositories;

use App\DataFixtures\TaskFixtures;
use App\DataFixtures\UserFixtures;
use App\Entity\User;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    use FixturesTrait;

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

    public function test_creating_user_succeeds_with_required_parameters()
    {
        $input = [
            'email' => 'user35@mail.com',
            'password' => '111111',
            'password_confirmation' => '111111'
        ];
        $user = $this->entityManager
            ->getRepository(User::class)
            ->create($input)
        ;

        $this->assertNotNull($user);
    }
}