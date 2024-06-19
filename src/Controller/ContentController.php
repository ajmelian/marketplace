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
     * Crea un nuevo contenido.
     *
     * @Route("/api/content", name="api_create_content", methods={"POST"})
     *
     * @param Request $request La solicitud HTTP con los datos JSON del contenido a crear.
     * @param Security $security El servicio de seguridad para obtener el usuario autenticado.
     * @return JsonResponse Una respuesta JSON indicando el éxito de la operación.
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
     * Lista todos los contenidos o filtra por título si se proporciona el parámetro 'title' en la consulta.
     *
     * @Route("/api/content", name="api_list_contents", methods={"GET"})
     *
     * @param Request $request La solicitud HTTP con los parámetros de consulta opcionales.
     * @return JsonResponse Una respuesta JSON con los contenidos recuperados.
     */
    public function listContents(Request $request)
    {
        $title = $request->query->get('title');
        $contents = $this->contentRepository->findByTitle($title); // Método personalizado en el repositorio

        return $this->json($contents);
    }

    /**
     * Obtiene un contenido específico por su ID.
     *
     * @Route("/api/content/{id}", name="api_get_content", methods={"GET"})
     *
     * @param Content $content El contenido recuperado automáticamente por su ID.
     * @return JsonResponse Una respuesta JSON con el contenido recuperado.
     */
    public function getContent(Content $content)
    {
        return $this->json($content);
    }

    /**
     * Actualiza un contenido existente.
     *
     * @Route("/api/content/{id}", name="api_update_content", methods={"PUT"})
     *
     * @param Request $request La solicitud HTTP con los datos JSON actualizados del contenido.
     * @param Content $content El contenido recuperado automáticamente por su ID.
     * @return JsonResponse Una respuesta JSON indicando el éxito de la operación.
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
     * Elimina un contenido existente.
     *
     * @Route("/api/content/{id}", name="api_delete_content", methods={"DELETE"})
     *
     * @param Content $content El contenido recuperado automáticamente por su ID.
     * @return JsonResponse Una respuesta JSON indicando el éxito de la operación.
     */
    public function deleteContent(Content $content)
    {
        $this->entityManager->remove($content);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Content deleted successfully']);
    }
}
