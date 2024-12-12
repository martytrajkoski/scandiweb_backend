<?php

namespace App\Resolver;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use App\Resolver\TypeRegistry;

class OrderInputType
{
    public static function getType(TypeRegistry $typeRegistry): InputObjectType
    {
        return new InputObjectType([
            'name' => 'OrderInput',
            'fields' => [
                'total_amount' => [
                    'type' => Type::nonNull(Type::float()),
                ],
                'items' => [
                    'type' => Type::listOf($typeRegistry->get('OrderItemInput')),
                ],
            ],
        ]);
    }
}
