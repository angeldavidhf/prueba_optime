<?php

namespace App\Controller;

use App\Repository\CategoriesRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoriesSiteController
 * @package App\Controller
 *
 * @Route(path="/categories")
 */

class CategoriesController
{
    private $categoriesRepository;

    public function __construct(CategoriesRepository $categoriesRepository)
    {
        $this->categoriesRepository = $categoriesRepository;
    }

    /**
     * @Route("/add", name="add_category", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $code = isset($data['code'])? $data['code'] : null;
        $name = isset($data['name'])? $data['name'] : null;
        $description = isset($data['description'])? $data['description'] : null;
        $active = isset($data['active'])? $data['active'] : null;

        if (empty($code) || empty($name) || empty($description) || empty($active)) {
            throw new NotFoundHttpException('Esperando parametros obligatorios!');
        }

        if(preg_match('/[^a-z0-9]+/i', $code)) {
            throw new NotFoundHttpException('No se permiten caracteres especiales en el codigo!');
        }

        if (strlen(trim($name)) < 2) {
            throw new NotFoundHttpException('El nombre debe ser de 2 caracteres como minimo!');
        }

        $category = $this->categoriesRepository->findOneBy(['code' => trim($code)]);

        if ($category) {
            throw new NotFoundHttpException('Ya existe una categoría con ese codigo!');
        }

        $category = $this->categoriesRepository->findOneBy(['name' => trim($name)]);

        if ($category) {
            throw new NotFoundHttpException('Ya existe una categoría con ese nombre!');
        }

        $this->categoriesRepository->saveCategory($code, $name, $description, $active);

        return new JsonResponse(['status' => 'Categoría Creada!'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/get/{id}", name="get_one_category", methods={"GET"})
     */
    public function getOneCategory($id): JsonResponse
    {
        $category = $this->categoriesRepository->findOneBy(['id' => $id]);

        $data = [
            'id' => $category->getId(),
            'code' => $category->getCode(),
            'name' => $category->getName(),
            'description' => $category->getDescription(),
            'active' => $category->getActive()
        ];

        return new JsonResponse(['category' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/get-all", name="get_all_categories", methods={"GET"})
     */
    public function getAllCategory(): JsonResponse
    {
        $categories = $this->categoriesRepository->findAll();
        $data = [];

        foreach ($categories as $category) {
            $data[] = [
                'id' => $category->getId(),
                'code' => $category->getCode(),
                'name' => $category->getName(),
                'description' => $category->getDescription(),
                'active' => $category->getActive()
            ];
        }

        return new JsonResponse(['categories' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/update/{id}", name="update_category", methods={"PUT"})
     */
    public function updateCategory($id, Request $request): JsonResponse
    {
        $category = $this->categoriesRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);

        if(preg_match('/[^a-z0-9]+/i', $data['code'])) {
            throw new NotFoundHttpException('No se permiten caracteres especiales en el codigo!');
        }

        if (strlen(trim($data['name'])) < 2) {
            throw new NotFoundHttpException('El nombre debe ser de 2 caracteres como minimo!');
        }

        $this->categoriesRepository->updateCategory($category, $data);

        return new JsonResponse(['status' => 'Categoría Actualizada!']);
    }

    /**
     * @Route("/delete/{id}", name="delete_category", methods={"DELETE"})
     */
    public function deleteCategory($id): JsonResponse
    {
        $category = $this->categoriesRepository->findOneBy(['id' => $id]);

        $this->categoriesRepository->removeCategory($category);

        return new JsonResponse(['status' => 'Categoría eliminado!']);
    }
}