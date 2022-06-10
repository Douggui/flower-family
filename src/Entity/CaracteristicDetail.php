<?php

namespace App\Entity;

use App\Repository\CaracteristicDetailRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CaracteristicDetailRepository::class)
 */
class CaracteristicDetail
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Caracteristic::class, inversedBy="caracteristicDetails")
     * @ORM\JoinColumn(nullable=false)
     */
    private $caracteristic;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $detail;

    /**
     * @ORM\ManyToOne(targetEntity=Product::class, inversedBy="caracteristicDetails")
     */
    private $product;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCaracteristic(): ?Caracteristic
    {
        return $this->caracteristic;
    }

    public function setCaracteristic(?Caracteristic $caracteristic): self
    {
        $this->caracteristic = $caracteristic;

        return $this;
    }

    public function getDetail(): ?string
    {
        return $this->detail;
    }

    public function setDetail(string $detail): self
    {
        $this->detail = $detail;

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
        return  $this->detail;
    }
}