<?php
// src/Controller/AuthenticationController.php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Security;

class AuthenticationController extends AbstractController
{
    private $entityManager;
    private $passwordEncoder;
    private $jwtManager;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, JWTTokenManagerInterface $jwtManager)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->jwtManager = $jwtManager;
    }

    /**
     * @Route("/api/register", name="api_register", methods={"POST"})
     */
    public function register(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword($this->passwordEncoder->encodePassword($user, $data['password']));

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'User registered successfully'], JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/api/login", name="api_login", methods={"POST"})
     */
    public function login(Request $request)
    {
        // Se ejecuta el autenticador configurado por LexikJWTAuthenticationBundle
        $user = $this->getUser();

        return new JsonResponse([
            'token' => $this->jwtManager->create($user)
        ]);
    }

    /**
     * @Route("/api/user", name="api_get_user", methods={"GET"})
     */
    public function getUserProfile(Security $security)
    {
        $user = $security->getUser();
        return new JsonResponse([
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $user->getRoles(),
        ]);
    }

    /**
     * @Route("/api/user", name="api_update_user", methods={"PUT"})
     */
    public function updateUserProfile(Request $request, Security $security)
    {
        $user = $security->getUser();
        $data = json_decode($request->getContent(), true);

        // Actualizar campos del perfil
        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }
        if (isset($data['password'])) {
            $user->setPassword($this->passwordEncoder->encodePassword($user, $data['password']));
        }

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'User profile updated successfully']);
    }
}
