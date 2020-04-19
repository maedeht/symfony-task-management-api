<?php

 namespace App\Controller;


 use App\Entity\User;
 use App\Request\CreateUserRequest;
 use App\Serializer\UserSerializer;
 use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
 use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
 use Symfony\Component\HttpFoundation\JsonResponse;
 use Symfony\Component\HttpFoundation\Response;
 use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
 use Symfony\Component\Security\Core\User\UserInterface;

 class AuthController extends ApiController
 {
     private $JWTManager;
     private $tokenStorageInterface;

     public function __construct(JWTTokenManagerInterface $JWTManager,
                                 TokenStorageInterface $tokenStorageInterface)
     {
         parent::__construct();
         $this->JWTManager = $JWTManager;
         $this->tokenStorageInterface = $tokenStorageInterface;
     }

     public function register(CreateUserRequest $createUserRequest)
     {
         $request = $this->transformJsonBody($this->getRequest());
         $data = $request->request->all();
         $violations = $createUserRequest->validate($data);
         if(!is_null($violations))
            return $violations;

         $data['username'] = $data['email'];

         $errors = [];
         if($data['password'] != $data['password_confirmation'])
             $errors[] = "Password does not match the password confirmation";

         if(!$errors)
         {
             try {
                 $user = $this->getDoctrine()
                     ->getRepository(User::class)
                     ->create($data);
                 $serialized = (new UserSerializer)->serialize($user);

                 return $this->respondWithSuccess('User successfully created', $serialized, Response::HTTP_CREATED);
             }
             catch (UniqueConstraintViolationException $e)
             {
                 $errors[] = "The email provided already has an account!";
             }
             catch (\Exception $e)
             {
                 $errors[] = "Unable to save new user at this time.";
             }
         }

         return $this->respondWithErrors($errors,Response::HTTP_BAD_REQUEST);
     }

     public function getTokenUser(UserInterface $user)
     {
         return new JsonResponse(['token' => $this->JWTManager->create($user)]);
     }

     public function profile()
     {
         $user = $this->tokenStorageInterface->getToken()->getUser();
         return $this->json([
             'user' => $user
         ]);
     }

 }