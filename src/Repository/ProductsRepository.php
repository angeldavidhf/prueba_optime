<?php

namespace App\Repository;

use App\Entity\Products;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Products|null find($id, $lockMode = null, $lockVersion = null)
 * @method Products|null findOneBy(array $criteria, array $orderBy = null)
 * @method Products[]    findAll()
 * @method Products[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductsRepository extends ServiceEntityRepository
{
    private $manager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, Products::class);
        $this->manager = $manager;
    }

    public function saveProduct($code, $name, $description, $brand, $price, $category_id)
    {
        $newProduct = new Products();

        $newProduct
            ->setCode($code)
            ->setName($name)
            ->setDescription($description)
            ->setBrand($brand)
            ->setPrice($price)
            ->setCategoryId($category_id);

        $this->manager->persist($newProduct);
        $this->manager->flush();
    }

    public function updateProduct(Products $product, $data)
    {
        empty($data['code']) ? true : $product->setCode($data['code']);
        empty($data['name']) ? true : $product->setName($data['name']);
        empty($data['description']) ? true : $product->setDescription($data['description']);
        empty($data['brand']) ? true : $product->setBrand($data['brand']);
        empty($data['price']) ? true : $product->setPrice($data['price']);
        empty($data['category_id']) ? true : $product->setCategoryId($data['category_id']);

        $this->manager->flush();
    }

    public function removeProduct(Products $product)
    {
        $this->manager->remove($product);
        $this->manager->flush();
    }
}
