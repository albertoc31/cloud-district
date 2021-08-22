<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    const MAX_LIST_PRODUCT = 5;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity=Tax::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tax;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price_with_tax;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getTax(): ?tax
    {
        return $this->tax;
    }

    public function setTax(?tax $tax): self
    {
        $this->tax = $tax;

        return $this;
    }

    public function getPriceWithTax(): ?float
    {
        return $this->price_with_tax;
    }

    public function setPriceWithTax(?float $price_with_tax): self
    {
        $this->price_with_tax = $price_with_tax;

        return $this;
    }

    /**
     * Usamos esta función para devolver todos los datos del producto
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'tax' => $this->getTax()->__toString(),
            'price_with_tax' => $this->price_with_tax,
        ];
    }
}
