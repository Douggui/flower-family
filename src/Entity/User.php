<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 *@UniqueEntity("email",message="L'email {{ value }} est déja utiliser",groups={"registration"})
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="ce champ ne pas pas être vide ",groups={"registration"})
     * @Assert\Email(
     *     message = "l'email {{ value }} n'est pas une email valide",groups={"registration"})
     * 
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="ce champ ne pas pas être vide ",groups={"registration"})
     * @Assert\Length(
     * min = 8,
     * minMessage="Votre mot de passe doit faire un minimun de 8 caractères",groups={"registration"})
     *
     *@Assert\Regex(
     * pattern="#(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).{8,}#",
     * match=true,
     * message="le mot de passe doit contenir au moins 1 chiffre, 1 lettre minuscule, 1 lettre majuscule et doit faire au moins 8 caractères",groups={"registration"})
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="ce champ ne pas pas être vide ",groups={"registration"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="ce champ ne pas pas être vide ",groups={"registration"})
     */
    private $lastName;

    /**
     * ORM\Column(type="string", length=255)
     * @Assert\EqualTo(propertyPath="password"  ,message="les deux mot de passe ne sont pas identique",groups={"registration"})
     */
    private $confirmPassword;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\OneToMany(targetEntity=ResetPassword::class, mappedBy="user", orphanRemoval=true)
     */
    private $resetPasswords;

    /**
     *
     * @Assert\NotBlank(message="ce champ ne pas pas être vide")
     * @Assert\Length(
     * min = 8,
     * minMessage="Votre mot de passe doit faire un minimun de 8 caractères")
     *
     *@Assert\Regex(
     * pattern="#(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z]).{8,}#",
     * match=true,
     * message="le mot de passe doit contenir au moins 1 chiffre, 1 lettre minuscule, 1 lettre majuscule et doit faire au moins 8 caractères")
     */
    private $newPassword;

    /**
     * 
     * @Assert\NotBlank(message="ce champ ne pas pas être vide ")
     * @Assert\EqualTo(propertyPath="newPassword"  ,message="les deux mot de passe ne sont pas identique")
     */
    private $confirmNewPassword;

    /**
     * ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="ce champ ne pas pas être vide ",groups={"resetPassword"})
     */
    private $oldPassword;

    /**
     * @ORM\OneToMany(targetEntity=Addresse::class, mappedBy="user", orphanRemoval=true)
     */
    private $addresses;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="user",cascade={"remove"})
     */
    private $orders;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="user",cascade={"remove"})
     */
    private $comments;

    public function __construct()
    {
        $this->resetPasswords = new ArrayCollection();
        $this->addresses = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }
    

    /**
     * @return Collection<int, ResetPassword>
     */
    public function getResetPasswords(): Collection
    {
        return $this->resetPasswords;
    }

    public function addResetPassword(ResetPassword $resetPassword): self
    {
        if (!$this->resetPasswords->contains($resetPassword)) {
            $this->resetPasswords[] = $resetPassword;
            $resetPassword->setUser($this);
        }

        return $this;
    }

    public function removeResetPassword(ResetPassword $resetPassword): self
    {
        if ($this->resetPasswords->removeElement($resetPassword)) {
            // set the owning side to null (unless already changed)
            if ($resetPassword->getUser() === $this) {
                $resetPassword->setUser(null);
            }
        }

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getConfirmNewPassword(): ?string
    {
        return $this->confirmNewPassword;
    }

    public function setConfirmNewPassword(string $confirmNewPassword): self
    {
        $this->confirmNewPassword = $confirmNewPassword;

        return $this;
    }

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    /**
     * @return Collection<int, Addresse>
     */
    public function getAddresses(): Collection
    {
        return $this->addresses;
    }

    public function addAddress(Addresse $address): self
    {
        if (!$this->addresses->contains($address)) {
            $this->addresses[] = $address;
            $address->setUser($this);
        }

        return $this;
    }

    public function removeAddress(Addresse $address): self
    {
        if ($this->addresses->removeElement($address)) {
            // set the owning side to null (unless already changed)
            if ($address->getUser() === $this) {
                $address->setUser(null);
            }
        }

        return $this;
    }
    public function getFullName(){
        return $this->getFirstName().' '.$this->getLastName();
    }
    public function __toString()
    {
        return $this->firstName.' '.$this->lastName;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setUser($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getUser() === $this) {
                $order->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }
}