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
     * Registra un nuevo usuario.
     *
     * @Route("/api/register", name="api_register", methods={"POST"})
     * 
     * @param Request $request La solicitud HTTP con datos de registro en formato JSON.
     * @return JsonResponse La respuesta JSON con un mensaje de éxito y el código HTTP 201 Created.
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
     * Inicia sesión y devuelve el token JWT.
     *
     * @Route("/api/login", name="api_login", methods={"POST"})
     * 
     * @param Request $request La solicitud HTTP de inicio de sesión.
     * @return JsonResponse La respuesta JSON con el token JWT generado.
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
     * Obtiene el perfil del usuario autenticado.
     *
     * @Route("/api/user", name="api_get_user", methods={"GET"})
     * 
     * @param Security $security El servicio de seguridad para obtener el usuario actual.
     * @return JsonResponse La respuesta JSON con los datos del usuario (id, email, roles).
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
     * Actualiza el perfil del usuario autenticado.
     *
     * @Route("/api/user", name="api_update_user", methods={"PUT"})
     * 
     * @param Request $request La solicitud HTTP con datos actualizados en formato JSON.
     * @param Security $security El servicio de seguridad para obtener el usuario actual.
     * @return JsonResponse La respuesta JSON con un mensaje de éxito.
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
