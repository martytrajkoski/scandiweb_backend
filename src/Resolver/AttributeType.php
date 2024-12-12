<?php

namespace App\Resolver;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use App\Resolver\TypeRegistry;

class AttributeType
{
    public static function getType(TypeRegistry $registry)
    {
        return new ObjectType([
            'name' => 'Attribute',
            'fields' => [
               'id' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => static function ($attribute) {
                        return $attribute->getId();
                    },
                ],
                'name' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => static function ($attribute) {
                        return $attribute->getName();
                    },
                ],
                'type' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => static function ($attribute) {
                        return $attribute->getType();
                    },
                ],
               'items' => [
                    'type' => Type::listOf($registry->get('AttributeItem')),
                    'resolve' => static function ($attribute) {
                        return $attribute->getItems();
                    },
                ],
            ],
        ]);
    }
}
