<?php

namespace App\Entity;

use App\Repository\OptionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OptionRepository::class)
 * @ORM\Table(name="`option`")
 */
class Option
{
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
     * @ORM\ManyToOne(targetEntity=Specification::class, inversedBy="options")
     * @ORM\JoinColumn(nullable=false)
     */
    private $specification;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="options")
     */
    private $product;

    /**
     * @ORM\OneToOne(targetEntity=Stock::class, mappedBy="productOption", cascade={"persist", "remove"})
     */
    private $stock;


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

    public function getSpecification(): ?Specification
    {
        return $this->specification;
    }

    public function setSpecification(?Specification $specification): self
    {
        $this->specification = $specification;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

   
    public function __toString()
    {
        return $this->name;
    }

    public function getStock(): ?Stock
    {
        return $this->stock;
    }

    public function setStock(?Stock $stock): self
    {
        // unset the owning side of the relation if necessary
        if ($stock === null && $this->stock !== null) {
            $this->stock->setProductOption(null);
        }

        // set the owning side of the relation if necessary
        if ($stock !== null && $stock->getProductOption() !== $this) {
            $stock->setProductOption($this);
        }

        $this->stock = $stock;

        return $this;
    }
   
}