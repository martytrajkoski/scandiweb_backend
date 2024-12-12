<?php

namespace App\Resolver;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use App\Resolver\TypeRegistry;

class OrderItemType
{
    public static function getType(TypeRegistry $registry): ObjectType
    {
        return new ObjectType([
            'name' => 'OrderItem',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::int()),
                    'resolve' => static function ($orderItem) {
                        return $orderItem->getId();
                    },
                ],
                'product_id' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => static function ($orderItem) {
                        return $orderItem->getProduct()->getId();
                    },
                ],
                'quantity' => [
                    'type' => Type::nonNull(Type::int()),
                    'resolve' => static function ($orderItem) {
                        return $orderItem->getQuantity();
                    },
                ],
                'selectedOptions' => [
                    'type' => Type::string(),
                    'resolve' => static function ($orderItem){
                        return $orderItem->getSelectedOptions();
                    }
                ]
            ],
        ]);
    }
}
