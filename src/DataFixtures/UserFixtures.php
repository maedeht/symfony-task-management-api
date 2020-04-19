<?php

namespace App\DataFixtures;


use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends BaseFixture
{
    const ADMIN_REFERENCE = 'admin';
    const USER_REFERENCE = 'user';

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        parent::__construct($passwordEncoder);
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Load data fixtures with the passed EntityManager
     */
    public function load(ObjectManager $objectManager)
    {
        parent::load($objectManager);
        $admin = new User();
        $admin->setUsername('admin@mail.com');
        $admin->setEmail('admin@mail.com');
        $admin->setPassword($this->passwordEncoder->encodePassword($admin, '111111'));
        $admin->setRoles([
            'ROLE_ADMIN'
        ]);
        $objectManager->persist($admin);

        $user = new User();
        $email = $this->faker->email;
        $user->setUsername($email);
        $user->setEmail($email);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $this->faker->password));
        $user->setRoles([
            'ROLE_USER'
        ]);
        $objectManager->persist($user);
        $objectManager->flush();

        $this->addReference(self::ADMIN_REFERENCE, $admin);
        $this->addReference(self::USER_REFERENCE, $user);

    }
}