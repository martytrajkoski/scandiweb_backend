<?php

namespace App\Repositories;

use App\Entities\Order;
use App\Entities\Product;
use App\Entities\OrderItem;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class OrderRepository
{
    private EntityManagerInterface $entityManager;
    private EntityRepository $orderRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->orderRepository = $entityManager->getRepository(Order::class);
    }

    public function create(array $data): Order
    {
        $order = new Order($data['total_amount']);
        $order->setTotalAmount($data['total_amount']);
        
        // Add order items to the order
        $orderItems = [];
        foreach ($data['items'] as $itemData) {
            $product = $this->entityManager->getRepository(Product::class)->find($itemData['product_id']);
            $item = new OrderItem($product, $itemData['quantity'], $itemData['selectedOptions']);
            $orderItems[] = $item;
        }
    
        $order->addItems($orderItems);
        
        // Persist and flush the order and items
        $this->entityManager->persist($order);
        $this->entityManager->flush();
        
        return $order;
    }
    

    public function all(): array
    {
        return $this->orderRepository->findAll();
    }

    public function find($id): ?Order
    {
        return $this->orderRepository->find($id);
    }
}
