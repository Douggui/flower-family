<?php

namespace App\Entity;

use App\Repository\AddresseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AddresseRepository::class)
 */
class Addresse
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="ce champ ne pas pas être vide ")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="ce champ ne pas pas être vide ")
     */
    private $addresse;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="ce champ ne pas pas être vide ")
     * @Assert\Regex(
     *  pattern="#^[0-9]{1}[0-9AB]{1}[0-9]{3}#",
     *  match=true,
     *  message=" le code postale n'est pas valide ",)
     * 
     */
    private $postal;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="ce champ ne pas pas être vide ")
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="ce champ ne pas pas être vide ")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="ce champ ne pas pas être vide ")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="ce champ ne pas pas être vide ")
     * @Assert\Regex(
     * pattern="#^[0-9]{10}#",
     * match=true,
     * message=" le numéro de téléphone est invalide ")
     * 
     */
    private $phone;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="addresses")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

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

    public function getAddresse(): ?string
    {
        return $this->addresse;
    }

    public function setAddresse(string $addresse): self
    {
        $this->addresse = $addresse;

        return $this;
    }

    public function getPostal(): ?string
    {
        return $this->postal;
    }

    public function setPostal(string $postal): self
    {
        $this->postal = $postal;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
    public function __toString()
    {
        return $this->name.'[br]'.$this->addresse.'[br]'.$this->postal.'[br]'.$this->city;
    }
    public function getFullAddresse()
    {
        return $this->name.'[br]'.$this->addresse.'[br]'.$this->postal.'[br]'.$this->city;
    }
}