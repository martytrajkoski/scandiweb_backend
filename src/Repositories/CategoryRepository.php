<?php

namespace App\Repositories;

use App\Entities\Category; // Assume this is your Category model
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Exception;

class CategoryRepository implements RepositoryInterface {
    private EntityManagerInterface $entityManager;
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(Category::class);
    }

    public function all(): array {
        return $this->repository->findAll();
    }

    public function find($id): ?Category {
        return $this->repository->find($id);
    }

    public function create(array $data): Category {
        $category = new Category($data['name']);
        
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        return $category;
    }

    public function update($id, array $data): Category {
        $category = $this->find($id);
        if (!$category) {
            throw new Exception('Category not found.');
        }
        
        $category->setName($data['name']);
        
        $this->entityManager->flush();
        return $category;
    }

    public function delete($id): bool {
        $category = $this->find($id);
        if (!$category) {
            throw new Exception('Category not found.');
        }

        $this->entityManager->remove($category);
        $this->entityManager->flush();
        return true;
    }
}
