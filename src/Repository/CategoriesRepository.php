<?php

namespace App\Repository;

use App\Entity\Categories;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @method Categories|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categories|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categories[]    findAll()
 * @method Categories[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoriesRepository extends ServiceEntityRepository
{
    private $manager;

    public function __construct(ManagerRegistry $registry, EntityManagerInterface $manager)
    {
        parent::__construct($registry, Categories::class);
        $this->manager = $manager;
    }

    public function saveCategory($code, $name, $description, $active)
    {
        $newCategory = new Categories();

        $newCategory
            ->setCode($code)
            ->setName($name)
            ->setDescription($description)
            ->setActive($active);

        $this->manager->persist($newCategory);
        $this->manager->flush();
    }

    public function updateCategory(Categories $category, $data)
    {
        empty($data['code']) ? true : $category->setCode($data['code']);
        empty($data['name']) ? true : $category->setName($data['name']);
        empty($data['description']) ? true : $category->setDescription($data['description']);
        empty($data['active']) ? true : $category->setActive($data['active']);

        $this->manager->flush();
    }

    public function removeCategory(Categories $category)
    {
        $this->manager->remove($category);
        $this->manager->flush();
    }
}
