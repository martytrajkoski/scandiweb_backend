<?php

namespace App\Resolver;

use App\Entities\Attribute;
use App\Entities\Product;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Resolver\TypeRegistry;
use App\Repositories\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;


class ProductType
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }
    
    public static function getType(TypeRegistry $typeRegistry): ObjectType
    {
        return new ObjectType([
            'name' => 'Product',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => static function ($product) {
                        return $product->getId();
                    },
                ],
                'name' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => static function ($product) {
                        return $product->getName();
                    },
                ],
                'inStock' => [
                    'type' => Type::nonNull(Type::boolean()),
                    'resolve' => static function ($product) {
                        return $product->getInStock();
                    },
                ],
                'gallery' => [
                    'type' => Type::listOf(Type::string()),
                    'resolve' => static function ($product) {
                        return $product->getGallery();
                    },
                ],
                'description' => [
                    'type' => Type::string(),
                    'resolve' => static function ($product) {
                        return $product->getDescription();
                    },
                ],
                'category' => [
                    'type' => $typeRegistry->get('Category'),
                    'resolve' => static function ($product) {
                        return $product->getCategory();
                    },
                ],
                'attribute' => [
                    'type' => Type::listOf($typeRegistry->get('Attribute')),
                    'resolve' => static function ($product) {
                        $attributesMap = [];
                        foreach ($product->getAttributeItems() as $attributeItem) {
                            $attribute = $attributeItem->getAttribute();

                        foreach ($attributeItem->getProducts() as $associatedProduct) {
                                $pivotProductId = $associatedProduct->getId();
                                if ($pivotProductId === $product->getId()) {
                                   if (!isset($attributesMap[$attribute->getId()])) {
                                        $attributesMap[$attribute->getId()] = new Attribute($attribute->getName(), $attribute->getType(), $attribute->getId());
                                    }
                                    $attributesMap[$attribute->getId()]->addItem($attributeItem);
                                    break; 
                                }
                            }   
                        }
                        return array_values($attributesMap);
                    },
                ],
                'prices' => [
                    'type' => Type::listOf($typeRegistry->get('Price')),
                    'resolve' => static function ($product) {
                        return $product->getPrices();
                    },
                ],
                'brand' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => static function ($product) {
                        return $product->getBrand();
                    },
                ],
            ],
        ]);
    }
}
