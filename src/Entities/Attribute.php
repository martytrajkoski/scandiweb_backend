<?php

namespace App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\InheritanceType("JOINED")]
#[ORM\DiscriminatorColumn(name: "discriminator", type: "string")] 
#[ORM\DiscriminatorMap(["attribute" => "Attribute", "attribute_item" => "AttributeItem"])]
class Attribute
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    protected string $id;

    #[ORM\Column(type: 'string')]
    protected string $name;

    #[ORM\Column(type: 'string')]
    protected string $type;

    #[ORM\OneToMany(mappedBy: 'attribute', targetEntity: AttributeItem::class, cascade:['persist', 'remove'])]
    protected iterable $items;

    public function __construct(string $name, string $type, string $id)
    {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->items = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }
    public function getItems()
    {
        return $this->items;
    }

    public function addItem(AttributeItem $item): void
    {
        if (!$this->items->contains($item)) {
            $this->items[] = $item;
        }
    }
}
