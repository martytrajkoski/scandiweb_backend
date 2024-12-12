<?php

namespace App\Resolver;

use App\Resolver\OrderInputType;
use App\Resolver\OrderItemInputType;
use App\Resolver\OrderItemType;
use App\Resolver\OrderType;
use App\Resolver\AttributeItemType;
use App\Resolver\AttributeType;
use App\Resolver\CategoryType;
use App\Resolver\CurrencyType;
use App\Resolver\PriceType;
use App\Resolver\ProductType;


class TypeRegistry
{
    private array $types = [];

    public function __construct()
    {
        $this->types = [
            'Category' => fn() => CategoryType::getType($this),
            'Attribute' => fn() => AttributeType::getType($this),
            'AttributeItem' => fn() => AttributeItemType::getType($this),
            'Currency' => fn() => CurrencyType::getType($this),
            'Price' => fn() => PriceType::getType($this),
            'Product' => fn() => ProductType::getType($this),
            'Order' => fn() => OrderType::getType($this),
            'OrderItem' => fn() => OrderItemType::getType($this),
            'OrderItemInput' => fn() => OrderItemInputType::getType($this),
            'OrderInput' => fn() => OrderInputType::getType($this)
        ];
    }

    public function get(string $typeName)
    {
        if (!isset($this->types[$typeName])) {
            throw new \RuntimeException("Type '{$typeName}' not found.");
        }

        if (is_callable($this->types[$typeName])) {
            $this->types[$typeName] = call_user_func($this->types[$typeName]);
        }

        return $this->types[$typeName];
    }
}
