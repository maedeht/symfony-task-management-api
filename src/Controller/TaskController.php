<?php

namespace App\Controller;

use App\Entity\Task;
use App\Request\CreateTaskRequest;
use App\Serializer\TaskSerializer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class TaskController extends ApiController
{
    private $tokenStorageInterface;

    public function __construct(TokenStorageInterface $tokenStorageInterface)
    {
        parent::__construct();
        $this->tokenStorageInterface = $tokenStorageInterface;
    }

    public function index()
    {
        $user = $this->tokenStorageInterface->getToken()->getUser();
        $tasks = $this->getDoctrine()
                        ->getRepository(Task::class)
                        ->userTasks($user->getId());
        $serialized = (new TaskSerializer)->serialize($tasks);

        return $this->respondWithSuccess( 'Today tasks retrieved', $serialized);
    }

    public function show($id)
    {
        try {
            $this->checkUserAccess($id);
            $task = $this->getDoctrine()
                ->getRepository(Task::class)
                ->find($id);
            $serialized = (new TaskSerializer)->serialize($task);

            return $this->respondWithSuccess( 'Task details retrieved', $serialized);
        }
        catch (\Exception $e)
        {
            $errors[] = $e->getMessage();
            return $this->respondWithErrors($errors,$e->getCode());
        }
    }

    public function create(CreateTaskRequest $createTaskRequest)
    {
        $request = $this->transformJsonBody($this->getRequest());
        $data = $request->request->all();
        $violations = $createTaskRequest->validate($data);
        if(!is_null($violations))
            return $violations;
        try {

            $user = $this->tokenStorageInterface->getToken()->getUser();
            $task = $this->getDoctrine()
                ->getRepository(Task::class)
                ->create($data, $user);
            $serialized = (new TaskSerializer)->serialize($task);

            return $this->respondWithSuccess( 'Task successfully created', $serialized, Response::HTTP_CREATED);
        }
        catch (\Exception $e)
        {
            $errors[] = $e->getMessage();;
        }
        return $this->respondWithErrors($errors,Response::HTTP_BAD_REQUEST);
    }

    public function update($id)
    {
        $request = $this->transformJsonBody($this->getRequest());
        $data = $request->request->all();
        try {
            $this->checkUserAccess($id);
            $task = $this->getDoctrine()
                ->getRepository(Task::class)
                ->update($data, $id);

            $serialized = (new TaskSerializer)->serialize($task);

            return $this->respondWithSuccess( 'Task successfully updated', $serialized);
        }
        catch (\Exception $e)
        {
            $errors[] = $e->getMessage();
            return $this->respondWithErrors($errors,$e->getCode());
        }
    }

    public function delete($id)
    {
        try {
            $this->checkUserAccess($id);
            $task = $this->getDoctrine()
                ->getRepository(Task::class)
                ->delete($id);

            return $this->respondWithSuccess('Task successfully deleted');
        }
        catch (\Exception $e)
        {
            $errors[] = $e->getMessage();
            return $this->respondWithErrors($errors,$e->getCode());
        }
    }

    private function checkUserAccess($id)
    {
        return $this->getDoctrine()
                    ->getRepository(Task::class)
                    ->checkUserAccess($id);

    }
}
