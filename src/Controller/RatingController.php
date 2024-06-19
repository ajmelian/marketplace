<?php
// src/Controller/RatingController.php

namespace App\Controller;

use App\Entity\Rating;
use App\Repository\RatingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class RatingController extends AbstractController
{
    private $entityManager;
    private $ratingRepository;

    public function __construct(EntityManagerInterface $entityManager, RatingRepository $ratingRepository)
    {
        $this->entityManager = $entityManager;
        $this->ratingRepository = $ratingRepository;
    }

    /**
     * Califica un contenido específico.
     *
     * @Route("/api/content/{id}/rate", name="api_rate_content", methods={"POST"})
     *
     * @param Request $request La solicitud HTTP recibida.
     * @param Content $content El contenido a calificar obtenido automáticamente desde la ruta.
     * @param Security $security El servicio de seguridad para obtener el usuario autenticado.
     * 
     * @return JsonResponse Una respuesta JSON indicando el éxito de la operación.
     */
    public function rateContent(Request $request, Content $content, Security $security)
    {
        $data = json_decode($request->getContent(), true);

        $rating = new Rating();
        $rating->setValue($data['value']);
        $rating->setUser($security->getUser());
        $rating->setContent($content);

        $this->entityManager->persist($rating);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Content rated successfully'], JsonResponse::HTTP_CREATED);
    }
}
