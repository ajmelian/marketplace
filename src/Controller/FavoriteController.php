<?php
// src/Controller/FavoriteController.php

namespace App\Controller;

use App\Entity\Favorite;
use App\Repository\FavoriteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class FavoriteController extends AbstractController
{
    private $entityManager;
    private $favoriteRepository;

    public function __construct(EntityManagerInterface $entityManager, FavoriteRepository $favoriteRepository)
    {
        $this->entityManager = $entityManager;
        $this->favoriteRepository = $favoriteRepository;
    }

    /**
     * Marca como Favorito un contenido específico para el usuario autenticado.
     *
     * @Route("/api/content/{id}/favorite", name="api_favorite_content", methods={"POST"})
     *
     * @param Request $request La solicitud HTTP que contiene los datos de la petición.
     * @param Content $content El contenido específico a marcar como favorito (obtenido automáticamente por Symfony).
     * @param Security $security Servicio de seguridad para obtener el usuario autenticado.
     *
     * @return JsonResponse Una respuesta JSON indicando el éxito de la operación.
     */
    public function favoriteContent(Request $request, Content $content, Security $security)
    {
        $favorite = new Favorite();
        $favorite->setUser($security->getUser());
        $favorite->setContent($content);

        $this->entityManager->persist($favorite);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Content favorited successfully'], JsonResponse::HTTP_CREATED);
    }

    /**
     * Lista todos los contenidos marcados como favoritos por el usuario autenticado.
     *
     * @Route("/api/content/favorites", name="api_list_favorites", methods={"GET"})
     *
     * @param Security $security Servicio de seguridad para obtener el usuario autenticado.
     *
     * @return JsonResponse Una respuesta JSON con los favoritos del usuario.
     */
    public function listFavorites(Security $security)
    {
        $user = $security->getUser();
        $favorites = $this->favoriteRepository->findBy(['user' => $user]);

        return $this->json($favorites);
    }
}
