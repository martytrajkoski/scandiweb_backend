<?php

namespace App\Resolver;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use App\Resolver\TypeRegistry;

class OrderType
{
    public static function getType(TypeRegistry $registry): ObjectType
    {
        return new ObjectType([
            'name' => 'Order',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::int()),
                    'resolve' => static function ($order) {
                        return $order->getId();
                    },
                ],
                'created_at' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => static function ($order) {
                        return $order->getCreatedAt()->format('Y-m-d H:i:s');
                    },
                ],
                'total_amount' => [
                    'type' => Type::nonNull(Type::float()),
                    'resolve' => static function ($order) {
                        return $order->getTotalAmount();
                    },
                ],
                'items' => [
                    'type' => Type::listOf($registry->get('OrderItem')),
                    'resolve' => static function ($order) {
                        return $order->getItems();
                    },
                ],
            ],
        ]);
    }
}
