<?php

namespace App\Entity;

use App\Repository\CaracteristicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CaracteristicRepository::class)
 */
class Caracteristic
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
     * ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=SubCategory::class, inversedBy="caracteristics")
     * @ORM\JoinColumn(nullable=false)
     */
    private $subCategory;

    /**
     * @ORM\OneToMany(targetEntity=CaracteristicDetail::class, mappedBy="caracteristic", orphanRemoval=true)
     */
    private $caracteristicDetails;

    public function __construct()
    {
        $this->caracteristicDetails = new ArrayCollection();
    }

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

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSubCategory(): ?SubCategory
    {
        return $this->subCategory;
    }

    public function setSubCategory(?SubCategory $subCategory): self
    {
        $this->subCategory = $subCategory;

        return $this;
    }

    /**
     * @return Collection<int, CaracteristicDetail>
     */
    public function getCaracteristicDetails(): Collection
    {
        return $this->caracteristicDetails;
    }

    public function addCaracteristicDetail(CaracteristicDetail $caracteristicDetail): self
    {
        if (!$this->caracteristicDetails->contains($caracteristicDetail)) {
            $this->caracteristicDetails[] = $caracteristicDetail;
            $caracteristicDetail->setCaracteristic($this);
        }

        return $this;
    }

    public function removeCaracteristicDetail(CaracteristicDetail $caracteristicDetail): self
    {
        if ($this->caracteristicDetails->removeElement($caracteristicDetail)) {
            // set the owning side to null (unless already changed)
            if ($caracteristicDetail->getCaracteristic() === $this) {
                $caracteristicDetail->setCaracteristic(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return $this->name;
    }
}