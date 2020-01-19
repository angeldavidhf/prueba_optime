<?php

namespace App\Controller;

use App\Repository\ProductsRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductsSiteController
 * @package App\Controller
 *
 * @Route(path="/products")
 */

class ProductsController
{
    private $productsRepository;

    public function __construct(ProductsRepository $productsRepository)
    {
        $this->productsRepository = $productsRepository;
    }

    /**
     * @Route("/add", name="add_product", methods={"POST"})
     */
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $code = isset($data['code'])? $data['code'] : null;
        $name = isset($data['name'])? $data['name'] : null;
        $description = isset($data['description'])? $data['description'] : null;
        $brand = isset($data['brand'])? $data['brand'] : null;
        $price = isset($data['price'])? $data['price'] : null;
        $category_id = isset($data['category_id'])? $data['category_id'] : null;

        if (empty($code) || empty($name) || empty($description) || empty($brand) || empty($price) || empty($category_id)) {
            throw new NotFoundHttpException('Esperando parametros obligatorios!');
        }

        if(preg_match('/[^a-z0-9]+/i', $code)) {
            throw new NotFoundHttpException('No se permiten caracteres especiales en el codigo!');
        }

        if (strlen(trim($code)) < 4) {
            throw new NotFoundHttpException('El codigo debe tener 4 caracteres como minimo!');
        }

        if (strlen(trim($code)) > 10) {
            throw new NotFoundHttpException('El codigo debe tener 10 caracateres como maximo!');
        }

        if (strlen(trim($name)) < 4) {
            throw new NotFoundHttpException('Ingrese un nombre valido!');
        }

        if (!is_numeric($price)) {
            throw new NotFoundHttpException('Ingrese un precio valido!');
        }

        $product = $this->productsRepository->findOneBy(['code' => trim($code)]);

        if ($product) {
            throw new NotFoundHttpException('Ya existe un producto con ese codigo!');
        }

        $product = $this->productsRepository->findOneBy(['name' => trim($name)]);

        if ($product) {
            throw new NotFoundHttpException('Ya existe un producto con ese nombre!');
        }

        $this->productsRepository->saveProduct($code, $name, $description, $brand, $price, $category_id);

        return new JsonResponse(['status' => 'Producto Creado!'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/get/{id}", name="get_one_product", methods={"GET"})
     */
    public function getOneProduct($id): JsonResponse
    {
        $product = $this->productsRepository->findOneBy(['id' => $id]);

        $data = [
            'id' => $product->getId(),
            'code' => $product->getCode(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'brand' => $product->getBrand(),
            'price' => $product->getPrice(),
            'category_id' => $product->getCategoryId(),
        ];

        return new JsonResponse(['product' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/get-all", name="get_all_products", methods={"GET"})
     */
    public function getAllProducts(): JsonResponse
    {
        $products = $this->productsRepository->findAll();
        $data = [];

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'code' => $product->getCode(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'brand' => $product->getBrand(),
                'price' => $product->getPrice(),
                'category_id' => $product->getCategoryId(),
            ];
        }

        return new JsonResponse(['products' => $data], Response::HTTP_OK);
    }

    /**
     * @Route("/update/{id}", name="update_product", methods={"PUT"})
     */
    public function updateProduct($id, Request $request): JsonResponse
    {
        $product = $this->productsRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(), true);

        
        if(preg_match('/[^a-z0-9]+/i', $data['code'])) {
            throw new NotFoundHttpException('No se permiten caracteres especiales en el codigo!');
        }

        if (strlen(trim($data['code'])) < 4) {
            throw new NotFoundHttpException('El codigo debe tener 4 caracteres como minimo!');
        }

        if (strlen(trim($data['code'])) > 10) {
            throw new NotFoundHttpException('El codigo debe tener 10 caracateres como maximo!');
        }
        
        if (strlen(trim($data['name'])) < 4) {
            throw new NotFoundHttpException('Ingrese un nombre valido!');
        }

        if (!is_numeric($data['price'])) {
            throw new NotFoundHttpException('Ingrese un precio valido!');
        }

        $this->productsRepository->updateProduct($product, $data);

        return new JsonResponse(['status' => 'Producto Actualizado!']);
    }

    /**
     * @Route("/delete/{id}", name="delete_product", methods={"DELETE"})
     */
    public function deleteProduct($id): JsonResponse
    {
        $product = $this->productsRepository->findOneBy(['id' => $id]);

        $this->productsRepository->removeProduct($product);

        return new JsonResponse(['status' => 'Producto eliminado!']);
    }
}