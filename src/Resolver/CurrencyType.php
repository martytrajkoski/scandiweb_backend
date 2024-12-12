<?php

namespace App\Resolver;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Resolver\TypeRegistry;

class CurrencyType
{
    public static function getType(TypeRegistry $typeRegistry): ObjectType
    {
        return new ObjectType([
            'name' => 'Currency',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::id()),
                    'resolve' => static function ($currency) {
                        return $currency->getId();
                    },
                ],
                'label' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => static function ($currency) {
                        return $currency->getLabel();
                    },
                ],
                'symbol' => [
                    'type' => Type::nonNull(Type::string()),
                    'resolve' => static function ($currency) {
                        return $currency->getSymbol();
                    },
                ],
            ],
        ]);
    }
}
