<?php

namespace App\Resolver;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Resolver\TypeRegistry;

class PriceType
{
    public static function getType(TypeRegistry $typeRegistry): ObjectType
    {
        return new ObjectType([
            'name' => 'Price',
            'fields' => [
                'amount' => [
                    'type' => Type::nonNull(Type::float()),
                    'resolve' => static function ($price) {
                        return $price->getAmount();
                    },
                ],
                'currency' => [
                    'type' => $typeRegistry->get('Currency'),
                    'resolve' => static function ($price) {
                        return $price->getCurrency();
                    },
                ],
            ],
        ]);
    }
}
