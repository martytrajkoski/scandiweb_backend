<?php

namespace App\Resolver;

use App\Resolver\TypeRegistry;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;

class CategoryType
{
    public static function getType(TypeRegistry $registry): ObjectType
    {
        return new ObjectType([
            'name' => 'Category',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => static function ($category) {
                        return $category->getId();
                    },
                ],
                'name' => [
                    'type' => Type::string(),
                    'resolve' => static function ($category) {
                        return $category->getName();
                    },
                ],
            ],
        ]);
    }
}
