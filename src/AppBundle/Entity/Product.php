<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Context\ExecutionContext;

/**
 * Product
 *
 * @ORM\Entity
 * @ORM\Table(name="product")
 */
class Product
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $externalId;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="products")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    protected $category;

    /**
     * @var Brand
     *
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="Brand")
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    protected $brand;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @var float
     *
     * @Assert\NotBlank()
     * @ORM\Column(type="decimal", scale=2)
     */
    protected $price;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Choice({"new", "confirm", "deliver", "complete", "cancel"})
     * @ORM\Column(type="string")
     */
    protected $status = 'new';

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    protected $active = true;

    /**
     * @ORM\OneToMany(targetEntity="ProductPicture", mappedBy="product")
     * @ORM\OrderBy({"sort" = "asc"})
     */
    protected $pictures;

    /**
     * @ORM\OneToMany(targetEntity="ProductFile", mappedBy="product")
     * @ORM\OrderBy({"sort" = "asc"})
     */
    protected $files;

    /**
     * @ORM\OneToMany(targetEntity="ProductVideo", mappedBy="product")
     * @ORM\OrderBy({"sort" = "asc"})
     */
    protected $videos;

    /**
     * @ORM\OneToMany(targetEntity="ProductProperty", mappedBy="product")
     * @ORM\OrderBy({"sort" = "asc"})
     */
    protected $properties;

    /**
     * Product constructor
     */
    public function __construct()
    {
        $this->pictures = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->videos = new ArrayCollection();
        $this->properties = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Product
     */
    public function setId(?int $id): Product
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getExternalId(): ?int
    {
        return $this->externalId;
    }

    /**
     * @param int $externalId
     * @return Product
     */
    public function setExternalId(?int $externalId): Product
    {
        $this->externalId = $externalId;
        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     * @return Product
     */
    public function setCategory(?Category $category): Product
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return Brand
     */
    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    /**
     * @param Brand $brand
     * @return Product
     */
    public function setBrand(?Brand $brand): Product
    {
        $this->brand = $brand;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Product
     */
    public function setTitle(?string $title): Product
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return float
     */
    public function getPrice(): ?float
    {
        return $this->price;
    }

    /**
     * @param float $price
     * @return Product
     */
    public function setPrice(?float $price): Product
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Product
     */
    public function setDescription(?string $description): Product
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return Product
     */
    public function setStatus(?string $status): Product
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): ?bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     * @return Product
     */
    public function setActive(?bool $active): Product
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->getTitle();
    }
}