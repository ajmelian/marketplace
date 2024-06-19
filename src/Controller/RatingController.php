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
     * @Route("/api/content/{id}/rate", name="api_rate_content", methods={"POST"})
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
