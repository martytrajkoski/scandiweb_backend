<?php

namespace App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity]
#[ORM\Table(name: 'orders')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', length: 100)]
    private int $id;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $totalAmount;

    #[ORM\OneToMany(mappedBy: 'order', targetEntity: OrderItem::class, cascade: ['persist', 'remove'])]
    private Collection $items;

    public function __construct(float $totalAmount)
    {
        $this->createdAt = new \DateTime();
        $this->totalAmount = $totalAmount;
        $this->items = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTotalAmount(): float
    {
        return $this->totalAmount;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItems(array $items): void
    {
        foreach ($items as $item) {
            if (!$item instanceof OrderItem) {
                throw new \InvalidArgumentException('Each item must be an instance of OrderItem.');
            }

            if (!$this->items->contains($item)) {
                $this->items[] = $item;
                $item->setOrder($this);
            }
        }
    }

    public function setTotalAmount(float $totalAmount): void
    {
        $this->totalAmount = $totalAmount;
    } 
}
