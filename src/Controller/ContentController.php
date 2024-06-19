<?php
// src/Controller/ContentController.php

namespace App\Controller;

use App\Entity\Content;
use App\Repository\ContentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class ContentController extends AbstractController
{
    private $entityManager;
    private $contentRepository;

    public function __construct(EntityManagerInterface $entityManager, ContentRepository $contentRepository)
    {
        $this->entityManager = $entityManager;
        $this->contentRepository = $contentRepository;
    }

    /**
     * @Route("/api/content", name="api_create_content", methods={"POST"})
     */
    public function createContent(Request $request, Security $security)
    {
        $data = json_decode($request->getContent(), true);

        $content = new Content();
        $content->setTitle($data['title']);
        $content->setDescription($data['description']);
        $content->setContent($data['content']);
        $content->setUser($security->getUser());

        $this->entityManager->persist($content);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Content created successfully'], JsonResponse::HTTP_CREATED);
    }

    /**
     * @Route("/api/content", name="api_list_contents", methods={"GET"})
     */
    public function listContents(Request $request)
    {
        $title = $request->query->get('title');
        $contents = $this->contentRepository->findByTitle($title); // Método personalizado en el repositorio

        return $this->json($contents);
    }

    /**
     * @Route("/api/content/{id}", name="api_get_content", methods={"GET"})
     */
    public function getContent(Content $content)
    {
        return $this->json($content);
    }

    /**
     * @Route("/api/content/{id}", name="api_update_content", methods={"PUT"})
     */
    public function updateContent(Request $request, Content $content)
    {
        $data = json_decode($request->getContent(), true);

        $content->setTitle($data['title']);
        $content->setDescription($data['description']);
        $content->setContent($data['content']);

        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Content updated successfully']);
    }

    /**
     * @Route("/api/content/{id}", name="api_delete_content", methods={"DELETE"})
     */
    public function deleteContent(Content $content)
    {
        $this->entityManager->remove($content);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Content deleted successfully']);
    }
}
