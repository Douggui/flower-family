<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use PhpParser\Node\Stmt\For_;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
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
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     * @Gedmo\Slug(fields={"name"})
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity=SubCategory::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $subCategory;

    /**
     * @ORM\OneToMany(targetEntity=CaracteristicDetail::class, mappedBy="product",cascade={"remove"})
     */
    private $caracteristicDetails;

    /**
     * @ORM\OneToMany(targetEntity=Image::class, mappedBy="product", orphanRemoval=true)
     */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity=Option::class, mappedBy="product",cascade={"remove"})
     */
    private $options;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="product",cascade={"remove"})
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=OrderDetails::class, mappedBy="product",cascade={"remove"})
     */
    private $orderDetails;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isBest;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isNew;

    /**
     * @ORM\Column(type="text")
     */
    private $meta_description;

    /**
     * @ORM\OneToMany(targetEntity=Stock::class, mappedBy="product", orphanRemoval=true)
     */
    private $stocks;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $meta_keywords;

    public function __construct()
    {
        $this->caracteristicDetails = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->options = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->orderDetails = new ArrayCollection();
        $this->stocks = new ArrayCollection();
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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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
            $caracteristicDetail->setProduct($this);
        }

        return $this;
    }

    public function removeCaracteristicDetail(CaracteristicDetail $caracteristicDetail): self
    {
        if ($this->caracteristicDetails->removeElement($caracteristicDetail)) {
            // set the owning side to null (unless already changed)
            if ($caracteristicDetail->getProduct() === $this) {
                $caracteristicDetail->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setProduct($this);
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Stock>
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    public function addStock(Stock $stock): self
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks[] = $stock;
            $stock->setProduct($this);
        }

        return $this;
    }

    public function removeStock(Stock $stock): self
    {
        if ($this->stocks->removeElement($stock)) {
            // set the owning side to null (unless already changed)
            if ($stock->getProduct() === $this) {
                $stock->setProduct(null);
            }
        }

        return $this;
    }
   
    /**
     * @return Collection<int, Option>
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(Option $option): self
    {
        if (!$this->options->contains($option)) {
            $this->options[] = $option;
            $option->setProduct($this);
        }

        return $this;
    }

    public function removeOption(Option $option): self
    {
        if ($this->options->removeElement($option)) {
            // set the owning side to null (unless already changed)
            if ($option->getProduct() === $this) {
                $option->setProduct(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return $this->name;
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
            $comment->setProduct($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getProduct() === $this) {
                $comment->setProduct(null);
            }
        }

        return $this;
    }

    public function getCommentFromUser(User $user){
        foreach ($this->comments as $comment) {
           if($comment->getUser()===$user) return $comment;
        }
        return null;
    }

    public function isProductFromUser(User $user){

        foreach ($user->getOrders() as $orders) {
            foreach ($orders->getOrderDetails() as $order) {
                
                if($order->getProduct()->getId() && $orders->getIsDelivered()) return$order->getProduct(); 
            }
        }
        return null;
    }

    /**
     * @return Collection<int, OrderDetails>
     */
    public function getOrderDetails(): Collection
    {
        return $this->orderDetails;
    }

    public function addOrderDetail(OrderDetails $orderDetail): self
    {
        if (!$this->orderDetails->contains($orderDetail)) {
            $this->orderDetails[] = $orderDetail;
            $orderDetail->setProduct($this);
        }

        return $this;
    }

    public function removeOrderDetail(OrderDetails $orderDetail): self
    {
        if ($this->orderDetails->removeElement($orderDetail)) {
            // set the owning side to null (unless already changed)
            if ($orderDetail->getProduct() === $this) {
                $orderDetail->setProduct(null);
            }
        }

        return $this;
    }
    public function getAvgRatings(){
        $sum=0;
        foreach ($this->comments as  $value) {
            $sum=$sum+$value->getRating();
        }
        if(count($this->comments)>0) return $sum/count($this->comments);
        return 0;
    }


    public function haveOptions(){
        if(count($this->options)>0) return true;
        return false;
    }

    public function getIsBest(): ?bool
    {
        return $this->isBest;
    }

    public function setIsBest(?bool $isBest): self
    {
        $this->isBest = $isBest;

        return $this;
    }

    public function getIsNew(): ?bool
    {
        return $this->isNew;
    }

    public function setIsNew(?bool $isNew): self
    {
        $this->isNew = $isNew;

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->meta_description;
    }

    public function setMetaDescription(string $meta_description): self
    {
        $this->meta_description = $meta_description;

        return $this;
    }

    public function getMetaKeywords(): ?string
    {
        return $this->meta_keywords;
    }

    public function setMetaKeywords(string $meta_keywords): self
    {
        $this->meta_keywords = $meta_keywords;

        return $this;
    }

    public function getTheStock(){

        foreach ($this->getStocks() as $stock) {
           return $stock->getStock();
            
        }
    }

   

}