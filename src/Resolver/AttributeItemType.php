<?php

namespace App\Resolver;

use App\Entities\AttributeItem;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use App\Resolver\TypeRegistry;

class AttributeItemType
{
    public static function getType(TypeRegistry $registry)
    {
        return new ObjectType([
            'name' => 'AttributeItem',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => static function ($attributeItems) {
                        return $attributeItems->getId();
                    },
                ],
                'displayValue' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => static function ($attributeItems) {
                        return $attributeItems->getDisplayValue();
                    },
                ],
                'value' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => static function ($attributeItems) {
                        return $attributeItems->getValue();
                    },
                ],
                // 'attribute' => [
                //     'type' => $registry->get('Attribute'), // This ensures the Attribute field is included
                //     'resolve' => static function ($attributeItems) {
                //         return $attributeItems->getAttribute(); // Assuming 'getAttribute()' fetches related Attribute entity
                //     },
                // ],
                // 'attribute' => [
                //     'type' => $registry->get('Product'),
                //     'resolve' => static function ($attributeItem) {
                //         return $attributeItem->getAttributeItems();
                //     },
                // ],
                // 'products' => [
                //     'type' => Type::listOf(Type::string()),
                //     'resolve' => static fn($attributeItem) => [],
                // ],
                // 'products' => [
                //     'type' => Type::listOf($registry->get('Product')),
                //     'resolve' => function($attributeItem) {
                //         return $attributeItem->getProducts(); // Fetch the IDs of related products
                //     }
                // ],
                // 'attribute' => [
                //     'type' => $registry->get('Attribute'),
                //     'resolve' => static function ($attributeItem) {
                //         return $attributeItem->getAttribute();
                //     },
                // ],
            //    'productAttributeItems' => [
            //         'type' => Type::listOf($registry->get('ProductAttributeItem')),
            //         'resolve' => static function ($attributeItem) {
            //             return $attributeItem->getProductAttributeItems();
            //         },
            //     ],
            ],
        ]);
    }
}
