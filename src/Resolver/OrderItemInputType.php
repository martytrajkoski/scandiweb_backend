<?php

namespace App\Resolver;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use App\Resolver\TypeRegistry;

class OrderItemInputType
{
    public static function getType(TypeRegistry $typeRegistry): InputObjectType
    {
        return new InputObjectType([
            'name' => 'OrderItemInput',
            'fields' => [
                'product_id' => [
                    'type' => Type::nonNull(Type::string()),
                ],
                'quantity' => [
                    'type' => Type::nonNull(Type::int()),
                ],
                'selectedOptions' => [
                    'type' => Type::string(),
                ]
            ],
        ]);
    }
}
