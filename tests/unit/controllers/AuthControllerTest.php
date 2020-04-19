<?php

namespace Tests\Unit\Controllers;

use App\DataFixtures\TaskFixtures;
use App\DataFixtures\UserFixtures;
use App\Traits\CommonTestTrait;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthControllerTest extends WebTestCase
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

    public function test_user_should_not_be_able_to_login_with_null_username()
    {
        $this->client->request('POST', '/api/users/login',[],[],
            [ 'CONTENT_TYPE' => 'application/json' ],
            '{ "password":"111111"}'
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function test_user_should_not_be_able_to_login_with_null_password()
    {
        $user = $this->create_a_user('user1@mail.com');
        $this->client->request('POST', '/api/users/login',[],[],
            [ 'CONTENT_TYPE' => 'application/json' ],
            '{ "username":"'.$user->getUsername().'"}'
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function test_user_should_not_be_able_to_login_with_wrong_username()
    {
        $this->client->request('POST', '/api/users/login',[],[],
            [ 'CONTENT_TYPE' => 'application/json' ],
            '{ "username":"blahblah","password":"111111"}'
        );

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function test_user_should_not_be_able_to_login_with_wrong_password()
    {
        $user = $this->create_a_user('user2@mail.com');
        $this->client->request('POST', '/api/users/login',[],[],
            [ 'CONTENT_TYPE' => 'application/json' ],
            '{ "username":"'.$user->getUsername().'","password":"1r3ew4"}'
        );

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function test_user_should_be_able_to_login_with_correct_username_and_password()
    {
        $user = $this->create_a_user('user3@mail.com');
        $this->client->request('POST', '/api/users/login',[],[],
            [ 'CONTENT_TYPE' => 'application/json' ],
        '{ "username":"'.$user->getUsername().'","password":"111111"}'
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function test_user_should_not_be_able_to_register_with_null_email()
    {
        $this->client->request('POST', '/api/users/register',[],[],[],
            '{ "password":"111111","password_confirmation":"111111"}'
        );

        $this->assertEquals(422, $this->client->getResponse()->getStatusCode());
    }

    public function test_user_should_not_be_able_to_register_with_null_password()
    {
        $this->client->request('POST', '/api/users/register',[],[],[],
            '{ "email":"user4@mail.com","password_confirmation":"111111"}'
        );

        $this->assertEquals(422, $this->client->getResponse()->getStatusCode());
    }

    public function test_user_should_not_be_able_to_register_with_null_password_confirmation()
    {
        $this->client->request('POST', '/api/users/register',[],[],[],
            '{ "email":"user5@mail.com","password":"111111"}'
        );

        $this->assertEquals(422, $this->client->getResponse()->getStatusCode());
    }

    public function test_user_should_not_be_able_to_register_with_password_length_shorter_than_6()
    {
        $this->client->request('POST', '/api/users/register',[],[],[],
            '{ "email":"user6@mail.com","password":"4565","password_confirmation":"4565"}'
        );

        $this->assertEquals(422, $this->client->getResponse()->getStatusCode());
    }

    public function test_user_should_not_be_able_to_register_with_unequal_password_and_password_confirmation()
    {
        $this->client->request('POST', '/api/users/register',[],[],[],
            '{ "email":"user7@mail.com","password":"456564","password_confirmation":"111111"}'
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function test_user_should_not_be_able_to_register_with_duplicated_email()
    {
        $user = $this->create_a_user('user8@mail.com');
        $this->client->request('POST', '/api/users/register',[],[],[],
            '{ "email":"'.$user->getEmail().'","password":"111111","password_confirmation":"111111"}'
        );

        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function test_user_should_be_able_to_register_with_correct_username_password_and_its_confirmation()
    {
        $this->client->request('POST', '/api/users/register',[],[],[],
            '{ "email":"user9@mail.com","password":"111111","password_confirmation":"111111"}'
        );

        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
    }

    public function test_unauthenticated_user_should_not_access_their_profile()
    {
        $this->client->request('GET', '/api/users/profile',[], [], []);

        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    public function test_authenticated_user_should_access_their_profile()
    {
        $token = $this->user_login('user10@mail.com');
        $this->client->request('GET', '/api/users/profile',[], [],
            [ 'HTTP_AUTHORIZATION' => 'Bearer '.$token ]
        );

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}