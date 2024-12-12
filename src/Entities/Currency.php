<?php

namespace App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'currencies')]
class Currency
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length:5)]
    private string $label;

    #[ORM\Column(type: 'string')]
    private string $symbol;

    #[ORM\OneToMany(mappedBy: 'currency', targetEntity: Price::class)]
    protected iterable $prices;

    public function __construct(string $label, string $symbol)
    {
        $this->label = $label;
        $this->symbol = $symbol;
        $this->prices = new ArrayCollection();
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function setSymbol(string $symbol): void
    {
        $this->symbol = $symbol;
    }
}
