<?php

namespace App\Repositories;

use App\Entities\Product;
use App\Entities\Category;
use App\Entities\AttributeItem;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

class ProductRepository implements RepositoryInterface {
    private EntityManagerInterface $entityManager;
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $this->entityManager->getRepository(Product::class);
    }

    public function getProductsWithAttributeItems(): array {
        $queryBuilder = $this->repository->createQueryBuilder('p')
            ->leftJoin('p.attributeItems', 'ai')
            ->leftJoin('ai.attribute', 'a')
            ->addSelect('ai', 'a')
            ->getQuery();

        return $queryBuilder->getResult();
    }

   public function getAttributesWithItems(): array {
        $queryBuilder = $this->repository->createQueryBuilder('a')
            ->leftJoin('a.items', 'ai')
            ->addSelect('ai')
            ->getQuery();

        $results = $queryBuilder->getResult();

        $attributes = [];

        foreach ($results as $attribute) {
            $attributeData = [
                'id' => $attribute->getId(),
                'name' => $attribute->getName(),
                'type' => $attribute->getType(),
                'items' => [],
            ];

            foreach ($attribute->getItems() as $item) {
                $attributeData['items'][] = [
                    'id' => $item->getId(),
                    'displayValue' => $item->getDisplayValue(),
                    'value' => $item->getValue(),
                    '__typename' => 'Attribute',
                ];
            }

            $attributes[] = $attributeData;
        }

        return $attributes;
    }


    /**
     * Fetch and group attributes by product.
     *
     * @param Product $product
     * @return array
     */
    public function getAttributeItemsForProduct(Product $product): array {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        $queryBuilder->select('ai')
            ->from(AttributeItem::class, 'ai')
            ->innerJoin('ai.products', 'p')
            ->where('p.id = :productId')
            ->setParameter('productId', $product->getId());

        return $queryBuilder->getQuery()->getResult();
    }


    public function all(): array {
        return $this->repository->findAll();
    }

    public function find($id): ?Product {
        return $this->repository->find($id);
    }

    public function create(array $data): Product {
        $category = $this->entityManager->getRepository(Category::class)->find($data['category']);
        if (!$category) {
            throw new Exception('Category not found.');
        }

        $product = new Product(
            $data['name'],
            $data['isStock'],
            $data['gallery'],
            $data['description'],
            $category,
            $data['brand']
        );

        if (isset($data['attributeItems']) && is_array($data['attributeItems'])) {
            foreach ($data['attributeItems'] as $itemData) {
                $attributeItem = $this->entityManager->getRepository(AttributeItem::class)->find($itemData['id']);
                if ($attributeItem) {
                    $product->addAttributeItem($attributeItem);
                }
            }
        }

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $product;
    }

    public function update($id, array $data): Product {
        $product = $this->find($id);
        if (!$product) {
            throw new Exception('Product not found.');
        }

        $product->setName($data['name']);
        $product->setInStock($data['isStock']);
        $product->setGallery($data['gallery']);
        $product->setDescription($data['description']);
        $product->setBrand($data['brand']);

        if (isset($data['category'])) {
            $category = $this->entityManager->getRepository(Category::class)->find($data['category']);
            if (!$category) {
                throw new Exception('Category not found.');
            }
            $product->setCategory($category);
        }

        if (isset($data['attributeItems']) && is_array($data['attributeItems'])) {
            if ($product->getAttributeItems() instanceof ArrayCollection) {
                $product->getAttributeItems()->clear();
            }
            foreach ($data['attributeItems'] as $itemData) {
                $attributeItem = $this->entityManager->getRepository(AttributeItem::class)->find($itemData['id']);
                if ($attributeItem) {
                    $product->addAttributeItem($attributeItem);
                }
            }
        }

        $this->entityManager->flush();
        return $product;
    }

    public function delete($id): bool {
        $product = $this->find($id);
        if (!$product) {
            throw new Exception('Product not found.');
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();
        return true;
    }
}
