<?php

namespace Tests\Unit\Controllers;

use App\DataFixtures\TaskFixtures;
use App\DataFixtures\UserFixtures;
use App\Traits\CommonTestTrait;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TaskControllerTest extends WebTestCase
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

    public function test_unauthenticated_user_should_not_access_their_task_lists()
    {
        $this->client->request('GET', '/api/tasks/',[], [], []);

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function test_authenticated_user_should_access_their_task_lists()
    {
        $token = $this->user_login('user11@mail.com');
        $this->client->request('GET', '/api/tasks/',[], [],
            [ 'HTTP_AUTHORIZATION' => 'Bearer '.$token ]
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function test_unauthenticated_user_should_not_access_a_task_details()
    {
        $task = $this->get_last_task();
        $this->client->request('GET', '/api/tasks/'.$task->getId(), [], [], []);

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function test_authenticated_user_should_not_access_a_task_of_other_users_details()
    {
        $token = $this->user_login('user12@mail.com');
        $task = $this->get_last_task();
        $this->set_task_user_different_user($task);

        $this->client->request('GET', '/api/tasks/'.$task->getId(), [], [],
            [ 'HTTP_AUTHORIZATION' => 'Bearer '.$token ]
        );

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function test_authenticated_user_should_access_a_task_which_does_not_exist()
    {
        $token = $this->user_login('user13@mail.com');

        $this->client->request('GET', '/api/tasks/-1', [], [],
            [ 'HTTP_AUTHORIZATION' => 'Bearer '.$token ]
        );

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function test_authenticated_user_should_access_a_task_details()
    {
        $token = $this->user_login('user14@mail.com');
        $user = $this->find_user_by_email('user14@mail.com');
        $task = $this->create_task_for_user($user);

        $this->client->request('GET', '/api/tasks/'.$task->getId(), [], [],
            [ 'HTTP_AUTHORIZATION' => 'Bearer '.$token ]
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function test_unauthenticated_user_should_not_access_to_create_a_task()
    {
        $this->client->request('POST', '/api/tasks/', [],[],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            '{ "title": "task-1", "description": "task-1 description", "start_time": "2020-4-30 14:37:13", "duration":"5"}'
        );

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function test_authenticated_user_should_not_be_able_to_create_a_task_with_null_title()
    {
        $token = $this->user_login('user15@mail.com');

        $this->client->request('POST', '/api/tasks/', [],[],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token
            ],
            '{ "description": "task-1 description", "start_time": "2020-4-30 14:37:13", "duration":"5"}'
        );

        $this->assertEquals(422, $this->client->getResponse()->getStatusCode());
    }

    public function test_authenticated_user_should_not_be_able_to_create_a_task_with_null_start_time()
    {
        $token = $this->user_login('user16@mail.com');

        $this->client->request('POST', '/api/tasks/', [],[],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token
            ],
            '{ "title": "task-1", "description": "task-1 description", "duration":"5"}'
        );

        $this->assertEquals(422, $this->client->getResponse()->getStatusCode());
    }

    public function test_authenticated_user_should_access_to_create_a_task_with_correct_parameters()
    {
        $token = $this->user_login('user17@mail.com');

        $this->client->request('POST', '/api/tasks/', [],[],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token
            ],
            '{ "title": "task-1", "description": "task-1 description", "start_time": "2020-4-30 14:37:13", "duration":"5"}'
        );

        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
    }

    public function test_unauthenticated_user_should_not_access_to_update_a_task()
    {
        $user = $this->create_a_user('user18@mail.com');
        $task = $this->create_task_for_user($user);

        $this->client->request('PUT', '/api/tasks/'.$task->getId(), [],[],
            [
                'CONTENT_TYPE' => 'application/json',
            ],
            '{ "title": "task-1-new", "description": "task-1-new description", "start_time": "2020-5-1 12:45:16", "duration":"5"}'
        );

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function test_authenticated_user_should_not_be_able_to_update_a_task_of_others()
    {
        $token = $this->user_login('user19@mail.com');
        $user2 = $this->create_a_user('user20@mail.com');
        $task = $this->create_task_for_user($user2);

        $this->client->request('PUT', '/api/tasks/'.$task->getId(), [],[],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token
            ],
            '{ "title": "task-1-new", "description": "task-1-new description", "start_time": "2020-5-1 12:45:16", "duration":"5"}'
        );

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function test_authenticated_user_should_not_be_able_to_update_a_task_which_does_not_exist()
    {
        $token = $this->user_login('user21@mail.com');

        $this->client->request('PUT', '/api/tasks/-1', [],[],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token
            ],
            '{ "title": "task-1-new", "description": "task-1-new description", "start_time": "2020-5-1 12:45:16", "duration":"5"}'
        );

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function test_authenticated_user_should_access_to_update_an_existed_task_of_themselves_with_correct_parameters()
    {
        $token = $this->user_login('user22@mail.com');
        $user = $this->find_user_by_email('user22@mail.com');
        $task = $this->create_task_for_user($user);

        $this->client->request('PUT', '/api/tasks/'.$task->getId(), [],[],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer '.$token
            ],
            '{ "title": "task-1-new", "description": "task-1-new description", "start_time": "2020-5-1 12:45:16", "duration":"5"}'
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function test_unauthenticated_user_should_not_access_to_delete_a_task()
    {
        $user = $this->create_a_user('user23@mail.com');
        $task = $this->create_task_for_user($user);

        $this->client->request('DELETE', '/api/tasks/'.$task->getId(), [],[], []);

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function test_authenticated_user_should_acccess_to_delete_a_task_of_others()
    {
        $token = $this->user_login('user24@mail.com');
        $user = $this->create_a_user('user25@mail.com');
        $task = $this->create_task_for_user($user);

        $this->client->request('DELETE', '/api/tasks/'.$task->getId(), [],[],
            [ 'HTTP_AUTHORIZATION' => 'Bearer '.$token ]
        );

        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function test_authenticated_user_should_not_be_able_to_delete_a_task_which_does_not_exist()
    {
        $token = $this->user_login('user26@mail.com');

        $this->client->request('DELETE', '/api/tasks/-1', [],[],
            [ 'HTTP_AUTHORIZATION' => 'Bearer '.$token ]
        );

        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function test_authenticated_user_should_access_to_delete_an_existed_task_of_themselves()
    {
        $token = $this->user_login('user27@mail.com');
        $user = $this->find_user_by_email('user27@mail.com');
        $task = $this->create_task_for_user($user);

        $this->client->request('DELETE', '/api/tasks/'.$task->getId(), [],[],
            [ 'HTTP_AUTHORIZATION' => 'Bearer '.$token ]
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

}