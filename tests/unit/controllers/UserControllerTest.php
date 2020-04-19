<?php

namespace Tests\Unit\Controllers;


use App\DataFixtures\TaskFixtures;
use App\DataFixtures\UserFixtures;
use App\Traits\CommonTestTrait;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    use FixturesTrait, CommonTestTrait;

    protected $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures([
            UserFixtures::class,
            TaskFixtures::class
        ]);
        $this->client = static::createClient();
    }
    public function test_unauthenticated_user_should_not_access_users_list()
    {
        $this->client->request('GET', '/api/users/',[], [], []);

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function test_authenticated_user_without_admin_role_should_not_access_users_list()
    {
        $token = $this->user_login('user28@mail.com');
        $this->client->request('GET', '/api/users/',[], [],
            [ 'HTTP_AUTHORIZATION' => 'Bearer '.$token ]
        );

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function test_authenticated_user_with_admin_role_should_access_users_list()
    {
        $token = $this->user_login('user29@mail.com',[ 'ROLE_ADMIN' ]);
        $this->client->request('GET', '/api/users/',[], [],
            [ 'HTTP_AUTHORIZATION' => 'Bearer '.$token ]
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function test_unauthenticated_user_should_not_access_a_users_information()
    {
        $user = $this->create_a_user('user30@mail.com');

        $this->client->request('GET', '/api/users/'.$user->getId(),[], [], []);

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function test_authenticated_user_without_admin_role_should_not_access_a_users_information()
    {
        $token = $this->user_login('user31@mail.com');
        $user = $this->find_user_by_email('user31@mail.com');

        $this->client->request('GET', '/api/users/'.$user->getId(),[], [],
            [ 'HTTP_AUTHORIZATION' => 'Bearer '.$token ]
        );

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function test_authenticated_user_with_admin_role_should_access_a_users_information()
    {
        $token = $this->user_login('user32@mail.com',[ 'ROLE_ADMIN' ]);
        $user = $this->find_user_by_email('user32@mail.com');

        $this->client->request('GET', '/api/users/'.$user->getId(),[], [],
            [ 'HTTP_AUTHORIZATION' => 'Bearer '.$token ]
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}