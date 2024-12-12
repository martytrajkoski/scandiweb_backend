<?php

namespace App\Entities;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entities\Category;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'string', length: 100)]
    protected string $id;

    #[ORM\Column(type: 'string')]
    protected string $name;

    #[ORM\Column(type: 'boolean')]
    protected bool $inStock;

    #[ORM\Column(type: 'json')]
    protected array $gallery;

    #[ORM\Column(type: 'text')]
    protected string $description;

    #[ORM\ManyToOne(targetEntity: Category::class)]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', nullable: false)]
    protected ?Category $category;

    #[ORM\ManyToMany(targetEntity: AttributeItem::class, inversedBy: 'product')]
    #[ORM\JoinTable(name: 'product_attribute_items')]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'attributeitem_id', referencedColumnName: 'id')]
    private Collection $attributeItems;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Price::class, cascade: ['persist', 'remove'])]
    protected Collection $prices;

    #[ORM\Column(type: 'string')]
    protected string $brand;

    public function __construct(
        string $name,
        bool $isStock,
        array $gallery,
        string $description,
        Category $category,
        string $brand,
    ) {
        $this->name = $name;
        $this->isStock = $isStock;
        $this->gallery = $gallery;
        $this->description = $description;
        $this->category = $category;
        $this->attributeItems = new ArrayCollection();
        $this->prices = new ArrayCollection();
        $this->brand = $brand;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getInStock(): bool
    {
        return $this->inStock;
    }

    public function setInStock(bool $isStock): void
    {
        $this->isStock = $isStock;
    }

    public function getGallery(): array
    {
        return $this->gallery;
    }

    public function setGallery(array $gallery): void
    {
        $this->gallery = $gallery;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getCategory(): Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    public function getAttributeItems(): Collection
    {
        return $this->attributeItems;
    }

    public function addAttributeItem(AttributeItem $attributeItem): void
    {
        if (!$this->attributeItems->contains($attributeItem)) {
            $this->attributeItems[] = $attributeItem;
            $attributeItem->addProduct($this);
        }
    }

    public function removeAttributeItem(AttributeItem $attributeItem): self
    {
        $this->attributeItems->removeElement($attributeItem);
        return $this;
    }

    public function getPrices(): Collection
    {
        return $this->prices;
    }

    public function setPrices(Price $prices): void
    {
        $this->prices[] = $prices;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): void
    {
        $this->brand = $brand;
    }
}
