<?php

namespace App\Traits;


use App\Entity\Task;
use App\Entity\User;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Component\HttpClient\HttpClient;

trait CommonTestTrait
{
    use FixturesTrait;

    public function create_a_user($email, $roles = [ 'ROLE_USER' ])
    {
        $user = new User();
        $user->setEmail($email);
        $user->setUsername($email);
        $userPasswordEncoder = self::getContainer()->get('security.password_encoder');
        $user->setPassword($userPasswordEncoder->encodePassword($user, '111111'));
        $user->setRoles($roles);

        $manager = self::getContainer()->get('doctrine.orm.default_entity_manager');
        $manager->persist($user);
        $manager->flush();

        return $user;
    }

    public function user_login($email, $roles = [ 'ROLE_USER' ])
    {
        $client = HttpClient::createForBaseUri('http://localhost:8000');
        $user = $this->create_a_user($email, $roles);
        $response = $client->request('POST', '/api/users/login',[
            'headers' => [
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode([
                "username" => $user->getUsername(),
                "password" => "111111"
            ])
        ]
        );
        $token = json_decode($response->getContent())->token;
        return $token;
    }

    public function get_last_user()
    {
        return self::getContainer()->get('doctrine')
            ->getRepository(User::class)
            ->findBy([],['id' => 'DESC'], 1, 0)[0];
    }

    public function set_task_user_different_user($task)
    {
        $other_user = $this->create_a_user('other@mail.com');
        $task->setUser($other_user);
        $manager = self::getContainer()->get('doctrine.orm.default_entity_manager');
        $manager->persist($task);
        $manager->flush();

        return true;
    }

    public function create_task_for_user($user)
    {
        $task = new Task();
        $task->setTitle('task1');
        $task->setStartTime((new \DateTime('now'))->format('Y-m-d H:i:s'));
        $task->setStatus('todo');
        $task->setUser($user);
        $manager = self::getContainer()->get('doctrine.orm.default_entity_manager');
        $manager->persist($task);
        $manager->flush();

        return $task;
    }

    public function find_user_by_email($email)
    {
        $user = self::getContainer()->get('doctrine')
            ->getRepository(User::class)->findOneByEmail($email);
        return $user;
    }

    public function create_an_unpersist_user()
    {
        $user = new User();
        $user->setEmail('ttt@mail.com');
        $user->setUsername('ttt@mail.com');
        $userPasswordEncoder = self::getContainer()->get('security.password_encoder');
        $user->setPassword($userPasswordEncoder->encodePassword($user, '111111'));
        $user->setRoles(['ROLE_USER']);

        return $user;
    }

    private function get_last_task()
    {
        return self::getContainer()->get('doctrine')
            ->getRepository(Task::class)
            ->findBy([],['id' => 'DESC'], 1, 0)[0];
    }
}