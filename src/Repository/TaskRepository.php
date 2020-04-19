<?php

namespace App\Repository;

use App\Entity\Task;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    private $JWTManager;
    private $tokenStorageInterface;

    public function __construct(ManagerRegistry $registry,
                                JWTTokenManagerInterface $JWTManager,
                                TokenStorageInterface $tokenStorageInterface)
    {
        parent::__construct($registry, Task::class);
        $this->JWTManager = $JWTManager;
        $this->tokenStorageInterface = $tokenStorageInterface;
    }

    public function create($data, $user)
    {
        $task = new Task();
        $task->setTitle($data['title']);
        if(isset($data['description']))
            $task->setDescription($data['description']);
        if(isset($data['duration']))
            $task->setDuration($data['duration']);
        $task->setStartTime($data['start_time']);
        $task->setStatus('TODO');
        $task->setUser($user);
        $entityManager = $this->getEntityManager();
        $entityManager->persist($task);
        $entityManager->flush();

        return $task;
    }

    public function update($data, $id)
    {
        $task = $this->find($id);
        isset($data['title']) ? $task->setTitle($data['title']) : null;
        isset($data['description']) ? $task->setDescription($data['description']) : null;
        isset($data['duration']) ? $task->setDuration($data['duration']) : null;
        isset($data['start_time']) ? $task->setStartTime($data['start_time']) : null;
        isset($data['status']) ? $task->setStatus($data['status']) : null;
        $task->setUpdatedAt(new \DateTime());
        $entityManager = $this->getEntityManager();
        $entityManager->persist($task);
        $entityManager->flush();

        return $task;
    }

    public function find($id, $lockMode = null, $lockVersion = null)
    {
        $task = parent::find($id);
        if(is_null($task))
            throw new NotFoundHttpException('Task could not be found',null, 404);
        return $task;
    }

    public function delete($id)
    {
        $task = $this->find($id);
        $entityManager = $this->getEntityManager();
        $entityManager->remove($task);
        $entityManager->flush();

        return $task;
    }

    public function userTasks($user_id)
    {
        $now = (new \DateTime())->format('Y-m-d H:i:s');

        return $this->createQueryBuilder('t')
                    ->andWhere('t.user = :user_id')
                    ->andWhere('t.start_time LIKE :now')
                    ->setParameter('user_id', $user_id)
                    ->setParameter('now', $now."%")
                    ->orderBy('t.start_time', 'ASC')
                    ->setMaxResults(10)
                    ->getQuery()
                    ->getResult();
    }

    public function checkUserAccess($id)
    {
        $task = $this->find($id);
        if($task->getUser()->id != $this->tokenStorageInterface->getToken()->getUser()->getId())
            throw new AccessDeniedException('Access denied!',403);
        return true;
    }

    // /**
    //  * @return Task[] Returns an array of Task objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Task
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
