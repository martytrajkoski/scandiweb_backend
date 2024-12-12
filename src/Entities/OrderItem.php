<?php

namespace App\Entities;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'ordersitems')]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', length: 100)]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'items')]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id', nullable: false)]
    private Order $order;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false)]
    private Product $product;
    
    #[ORM\Column(type: 'integer')]
    private int $quantity;

    #[ORM\Column(type: 'string')]
    private string $selectedOptions;

    public function __construct(Product $product, int $quantity, string $selectedOptions)
    {
        $this->product = $product;
        $this->quantity = $quantity;
        $this->selectedOptions = $selectedOptions;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): void
    {
        $this->order = $order;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }
    
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getSelectedOptions(): string
    {
        return $this->selectedOptions;
    }

    public function setSelectedOptions(string $selectedOptions): void
    {
        $this->selectedOptions = $selectedOptions;
    }
}
