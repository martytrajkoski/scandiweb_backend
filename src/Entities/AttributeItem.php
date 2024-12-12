<?php

namespace App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class AttributeItem
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    protected string $id;

    #[ORM\Column(type: 'string')]
    protected string $displayValue;

    #[ORM\Column(type: 'string')]
    protected string $value;

    #[ORM\ManyToOne(targetEntity: Attribute::class)]
    #[ORM\JoinColumn(name: 'attribute_id', referencedColumnName: 'id', nullable: false )]
    protected Attribute $attribute;

    #[ORM\ManyToMany(targetEntity: Product::class, mappedBy: 'attributeItems')]
    protected Collection $products;

    public function __construct(string $id, string $displayValue, string $value, Attribute $attribute)
    {
        $this->id = $id;
        $this->displayValue = $displayValue;
        $this->value = $value;
        $this->attribute = $attribute;
        $this->products = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getDisplayValue(): string
    {
        return $this->displayValue;
    }

    public function getValue(): string
    {
        return $this->value;
    }
    
    public function getAttribute()
    {
        return $this->attribute;
    }

    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function setDisplayValue(string $displayValue): void
    {
        $this->displayValue = $displayValue;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function addAttribute(?Attribute $attribute): void
    {
        $this->attribute = $attribute;
    }

    public function addProduct(Product $product): void
    {
        $this->products[] = $product;
    }
}
