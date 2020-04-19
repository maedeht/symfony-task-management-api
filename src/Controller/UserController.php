<?php

namespace App\Controller;

use App\Serializer\UserSerializer;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;


class UserController extends ApiController
{
    public function index()
    {
        $users = $this->getDoctrine()
                    ->getRepository(User::class)
                    ->findAll();
        $serialized = (new UserSerializer)->serialize($users);

        return $this->respondWithSuccess('Users list successfully retrieved', $serialized, Response::HTTP_OK);
    }

    public function show($id)
    {
        $user = $this->getDoctrine()
                    ->getRepository(User::class)
                    ->find($id);
        $serialized = (new UserSerializer)->serialize($user);

        return $this->respondWithSuccess('User details successfully retrieved', $serialized, Response::HTTP_OK);
    }

}
